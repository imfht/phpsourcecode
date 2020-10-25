<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Sql\MySQL;

use Exception;
use Ke\Adm\Adapter\DbAdapter;
use Ke\Adm\Model;
use Ke\Adm\Query;
use Ke\Adm\Sql\ForgeImpl;

class Forge implements ForgeImpl
{

	/** @var DbAdapter */
	private $adapter = null;

	private $intTypes = [
		'int'      => 1,
		'smallint' => 1,
		'tinyint'  => 1,
	];

	private $floatTypes = [
		'float'   => 1,
		'double'  => 1,
		'decimal' => 1,
	];

	private $queryTable = [
		'select' => [
			'TABLE_NAME as name',
			'TABLE_TYPE as type',
			'ENGINE as engine',
			'TABLE_COMMENT as comment',
		],
		'from'   => 'information_schema.tables',
	];

	private $queryColumns = [
		'select' => [
			'COLUMN_NAME as column_name',
			'COLUMN_DEFAULT as column_default',
			'IS_NULLABLE as is_null',
			'DATA_TYPE as data_type',
			'CHARACTER_MAXIMUM_LENGTH as char_length',
			'COLUMN_KEY as column_key',
			'EXTRA as extra',
			'COLUMN_COMMENT as comment',
			'COLUMN_TYPE as column_type',
			'NUMERIC_PRECISION as numeric_precision',
			'NUMERIC_SCALE as numeric_scale',
		],
		'from'   => 'information_schema.columns',
		'order'  => 'ordinal_position ASC',
	];

	public function __construct(DbAdapter $adapter)
	{
		$this->adapter = $adapter;
	}

	public function buildTableProps(string $table, array $columns = []): array
	{
		$vars = [
			'columns' => '',
			'props'   => '',
		];
		$tableInfo = $this->getTableInfo($table);

		if ($tableInfo === false)
			throw new Exception("Table \"{$table}\" did not exist");

		$pkField = $this->getPkField($table);
		$pkField = mb_strtolower($pkField);
		$pkAutoInc = false;

		$dbCols = $this->getTableColumns($table);
		$props = [];
		$cols = [];
		$typeMaxLength = 0;
		$fieldMaxLength = 0;

		foreach ($dbCols as $column) {
			// foreach@start

			$field = mb_strtolower($column['column_name']);
			$type = $column['data_type'];
			$varType = null;
			$col = $this->filterColumn([]);

			// 匹配主键自增属性
			if ($field === $pkField && stripos($column['extra'], 'auto_increment') !== false) {
				$pkAutoInc = true;
			}

			// 提取字段的注释内容
			$comment = str_replace(['，', '：'], [',', ':'], $column['comment']);
			$comment = explode('|', $comment);

			if (!empty(($label = trim($comment[0]))))
				$col['label'] = $label;

			// 时间戳字段名
			if (preg_match('#_(at|time|date)#i', $field)) {
				if ($type === 'bigint' || $type === 'int' || isset($this->floatTypes[$type])) {
					$col['timestamp'] = 1;
					$varType = 'int';
				} else if (isset($this->floatTypes[$type])) {
					$col['timestamp'] = 1;
					$varType = 'float';
				} else {
					$col['datetime'] = 'datetime';
					$varType = 'string';
				}
				if (preg_match('#^create(d)?_#', $field)) {
					$col[self::ON_CREATE] = 'now';
				} else if (preg_match('#^update(d)?_#', $field)) {
					$col[self::ON_SAVE] = 'now'; // 把 update(d)_at time date 这些字段，改为 on_save 事件则填充，这样确保两个字段都有值，不会空
				}
				$props[] = [$varType, "{$field}", $label];
			} else {
				// 整形
				if (isset($this->intTypes[$type])) {
					$col['int'] = 1;

					if (isset($column['column_default'])) {
						$col['default'] = intval($column['column_default']);
						if ($col['default'] === false)
							$col['default'] = 0;
					}

					$varType = 'int';
					$props[] = [$varType, "{$field}", $label];
				} // 浮点
				elseif (isset($this->floatTypes[$type])) {
					$col['float'] = $column['numeric_scale'] > 0 ? intval($column['numeric_scale']) : 1;

					if (isset($column['column_default'])) {
						$col['default'] = floatval($column['column_default']);
						if ($col['default'] === false)
							$col['default'] = 0.00;
					}

					$varType = 'double';
					$props[] = [$varType, "{$field}", $label];
				} // bigint
				elseif ($type === 'bigint') {
					$col['int'] = 1;

					if (isset($column['column_default']) && is_numeric($column['column_default']))
						$col['default'] = intval($column['column_default']);

					$varType = 'int';
					$props[] = [$varType, "{$field}", $label];
				} // 枚举
				elseif ($type === 'enum') {
					// 枚举类型，肯定是要限制选项的
					$options = $col['options'] ?? [];
					if (!is_array($options))
						$options = [];

					$commentOptions = [];
					if (!empty($comment[1])) {
						$exp = explode(',', $comment[1]);
						foreach ($exp as $item) {
							$itemExp = explode(':', $item);
							if (isset($itemExp[1]))
								$commentOptions[trim($itemExp[0])] = trim($itemExp[1]);
						}
					}
					if (preg_match_all('#\'([^\']+)\'#i', $column['column_type'], $matches, PREG_SET_ORDER)) {
						foreach ($matches as $match) {
							$val = $match[1];
							$txt = isset($commentOptions[$match[1]]) ? $commentOptions[$match[1]] : $match[1];
							$options[$val] = $txt;
						}
					}
					$col['options'] = $options;
					if (isset($column['column_default'])) {
						$col['default'] = $this->getDefaultString($column['column_default']);
					}

					$varType = 'string';
					$props[] = [$varType, "{$field}", $label];
				} // 字符串类型
				elseif ($type === 'varchar' || $type === 'char') {
					$col['max'] = $column['char_length'];

					if (isset($column['column_default'])) {
						$default = $this->getDefaultString($column['column_default']);
						$defaultUpper = mb_strtoupper($default);
						if ($defaultUpper === 'NULL') // any db management tools such as "Navicat Premium"
							$default = '';
						$col['default'] = $default;
					} else {
						$col['default'] = '';
					}

					$varType = 'string';
					$props[] = [$varType, "{$field}", $label];
				} // 其他类型
				else {
					$varType = 'mixed';
					$props[] = [$varType, "{$field}", $label];
				}
			}

			// 如果是主键
			if ($field === $pkField) {
				$col['pk'] = 1;
				if ($pkAutoInc)
					$col['autoInc'] = 1;
			}

			$cols[$field] = $col;

			$typeLength = strlen($varType);
			$fieldLength = strlen($field);
			if ($fieldLength > $fieldMaxLength)
				$fieldMaxLength = $fieldLength;
			if ($typeLength > $typeMaxLength)
				$typeMaxLength = $typeLength;

			// foreach@close
		}

		$vars['pk'] = empty($pkField) ? "null" : "'{$pkField}'";
		$vars['tableName'] = "'{$table}'";

		$vars['pkAutoInc'] = empty($pkAutoInc) ? "false" : "true";

		$temp = [
			"\t\t// database columns",
			"\t\treturn [",
		];
		foreach ($cols as $field => $col) {
			$prefix = "\t\t\t";
			$temp[] = sprintf('%s%s => %s,',
				$prefix,
				str_pad(var_export($field, true), $fieldMaxLength + 2, ' ', STR_PAD_RIGHT),
				$this->exportColumn($col));
		}
		$temp[] = "\t\t];";
		$temp[] = "\t\t// database columns";
		$vars['columns'] = implode(PHP_EOL, $temp);

		$temp = [
			" * // class properties",
		];
		$vars['props'] = '';
		foreach ($props as $index => $prop) {
			$prefix = " * ";
			$temp[] = sprintf('%s@property %s $%s %s',
				$prefix,
				str_pad("{$prop[0]}", $typeMaxLength, ' ', STR_PAD_RIGHT),
				str_pad("{$prop[1]}", $fieldMaxLength, ' ', STR_PAD_RIGHT),
				$prop[2]);
		}
		$temp[] = " * // class properties";
		$vars['props'] = implode(PHP_EOL, $temp);

		return $vars;
	}

	public function filterColumn(array $column)
	{
		if (!empty($column)) {
			foreach (self::PROCESS_MAPS as $modelProc => $replaceProc) {
				if (isset($column[$modelProc])) {
					$column[$replaceProc] = $column[$modelProc];
					unset($column[$modelProc]);
				}
			}
		}
		return $column;
	}

	public function newQuery()
	{
		return new Query($this->adapter->getSourceName());
	}

	public function exportColumn($column)
	{
		if (!is_array($column)) {
			$export = '[]';
		} else {
			$export = var_export($column, true);
			$export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
			$array = preg_split("/\r\n|\n|\r/", $export);
			$array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/", "/^\s{1,}/"], [
				null,
				']$1',
				' => [',
				'',
			], $array);
			$export = join('', array_filter(["["] + $array));
			$export = str_replace(array_keys(self::PROCESS_EXPORT_MAPS), array_values(self::PROCESS_EXPORT_MAPS), $export);
		}
		return $export;
	}

	public function getDefaultString($defaultValue)
	{
		return trim($defaultValue, '\'"');
	}

	public function getDbTables(string $db = null): array
	{
		if (empty($db))
			$db = $this->adapter->getDatabase();
		if (empty($db))
			throw new Exception('Undefined database!');

		return $this->newQuery()->load($this->queryTable)->in([
			'TABLE_SCHEMA' => $db,
		])->find();
	}

	public function getTableInfo(string $table)
	{
		return $this->newQuery()->load($this->queryTable)->in([
			'TABLE_NAME'   => $table,
			'TABLE_SCHEMA' => $this->adapter->getDatabase(),
		])->findOne();
	}

	public function getPkField(string $table)
	{
		return $this->newQuery()->select('COLUMN_NAME as column_name')->from('information_schema.key_column_usage')->in([
			'TABLE_NAME'      => $table,
			'TABLE_SCHEMA'    => $this->adapter->getDatabase(),
			'constraint_name' => 'PRIMARY',
		])->columnOne(0);
	}

	public function getTableColumns(string $table)
	{
		return $this->newQuery()->load($this->queryColumns)->in([
			'TABLE_NAME'   => $table,
			'TABLE_SCHEMA' => $this->adapter->getDatabase(),
		])->find();
	}

	public function mkLabel($field, $label)
	{
		return sprintf('\'%s\' => \'%s\'', 'label', $label);
	}
}