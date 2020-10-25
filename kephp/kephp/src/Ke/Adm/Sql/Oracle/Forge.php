<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm\Sql\Oracle;


use Exception;
use Ke\Adm\Adapter\Db\PdoOracle;
use Ke\Adm\Adapter\DbAdapter;
use Ke\Adm\Query;
use Ke\Adm\Sql\ForgeImpl;

class Forge implements ForgeImpl
{

	private $queryTables = [
		'select' => [
			'TABLE_NAME as name',
		],
		'from'   => 'user_tables',
	];

	private $intTypes = [
		'NUMBER'  => 1,
		'INT'     => 1,
		'INTEGER' => 1,
	];

	private $floatTypes = [
		'NUMBER'           => 1,
		'FLOAT'            => 1,
		'DOUBLE PRECISION' => 1,
		'DECIMAL'          => 1,
	];

	private $stringType = [
		'VARCHAR2'  => 1,
		'VARCHAR'   => 1,
		'NVARCHAR2' => 1,
		'CHAR'      => 1,
		'NCHAR'     => 1,
	];

	/**
	 * @var PdoOracle
	 */
	private $adapter;

	public function __construct(PdoOracle $adapter)
	{
		$this->adapter = $adapter;
	}

	public function getDbTables(string $db = null): array
	{
		return (new Query())->load($this->queryTables)->in([
			'STATUS' => 'VALID',
		])->find();
	}

	public function getTableColumns(string $table)
	{
		$sql = "SELECT 
USER_TAB_COLS.TABLE_NAME as table_name,
USER_TAB_COLS.COLUMN_NAME as column_name, 
USER_TAB_COLS.DATA_TYPE as data_type, 
USER_TAB_COLS.DATA_LENGTH as length,
USER_TAB_COLS.DATA_PRECISION as precision,
USER_TAB_COLS.DATA_SCALE as scale,
USER_TAB_COLS.DATA_DEFAULT as column_default,
USER_TAB_COLS.NULLABLE as nullable, 
USER_TAB_COLS.COLUMN_ID as column_idx,
user_col_comments.comments as comments FROM USER_TAB_COLS 
inner join user_col_comments on user_col_comments.TABLE_NAME=USER_TAB_COLS.TABLE_NAME and user_col_comments.COLUMN_NAME=USER_TAB_COLS.COLUMN_NAME 
WHERE USER_TAB_COLS.TABLE_NAME = ?";

		return $this->adapter->query($sql, [$table], DbAdapter::MULTI);
	}

	public function getPkField(string $table)
	{
		$sql = "select cu.column_name from user_cons_columns cu, user_constraints au where cu.constraint_name = au.constraint_name and au.constraint_type = 'P' and au.table_name = ?";
		$field = $this->adapter->query($sql, [$table], DbAdapter::ONE, DbAdapter::FETCH_COLUMN, 0);
		if (!empty($field))
			return mb_strtolower($field);
		return null;
	}

	public function buildTableProps(string $table, array $columns = []): array
	{
		$vars = [
			'columns' => '',
			'props'   => '',
		];

		$config = $this->adapter->getConfiguration();

		$pkField = $this->getPkField($table);
		$pkAutoInc = false;

		$dbCols = $this->getTableColumns($table);
		if (empty($dbCols))
			throw new Exception("Table \"{$table}\" did not exist");
		$props = [];
		$cols = [];
		$typeMaxLength = 0;
		$fieldMaxLength = 0;

		foreach ($dbCols as $column) {
			// foreach@start

			$field = mb_strtolower($column['column_name']);
			$type = $column['data_type'];
			$isNumber = $this->isNumber($type);
			$varType = null;
			$col = $this->filterColumn([]);

			// oracle 处理方式不同于 mysql
			$length = intval($column['length']);
			$precision = intval($column['precision']);
			$scale = intval($column['scale']);

			if ($field === $config['defaultPk']) {
				if (empty($pkField)) { // 如果没匹配到 主键
					$pkField = $field;
				}
				if ($isNumber && !empty($config['defaultAutoInc'])) {
					$pkAutoInc = true; // oracle 的自增都是通过 seq 实现的
				}
			}

			// 提取字段的注释内容
			$comment = str_replace(['，', '：'], [',', ':'], $column['comments']);
			$comment = explode('|', $comment);

			if (!empty(($label = trim($comment[0]))))
				$col['label'] = $label;

			// 特定字段的匹配测试
			if (preg_match('#create(d)?_at#', $field)) {
				$col[self::ON_CREATE] = 'now';
			} elseif (preg_match('#update(d)?_at#', $field)) {
				$col[self::ON_UPDATE] = 'now';
			} elseif (preg_match('#save(d)?_at#', $field)) {
				$col[self::ON_SAVE] = 'now';
			}

			// 时间戳类型
			if ($type === 'DATE') {
				$col['datetime'] = 1;
				$varType = 'string';
			} elseif ($isNumber) {
				if ($this->isFloat($type) && $scale > 0) {
					$varType = 'double';
					$col['float'] = $scale;
					$col['default'] = floatval($column['column_default']);
					if ($col['default'] === false)
						$col['default'] = 0.00;
				} else {
					$varType = 'int';
					$col['int'] = 1;
					$col['default'] = intval($column['column_default']);
					if ($col['default'] === false)
						$col['default'] = 0;
				}
			} elseif ($this->isString($type)) {
				$varType = 'string';
				$col['max'] = $length;

				if (isset($column['column_default'])) {
					$default = $column['column_default'];
					$defaultUpper = mb_strtoupper($default);
					if ($defaultUpper === 'NULL') // any db management tools such as "Navicat Premium"
						$default = '';
					$col['default'] = $default;
				} else {
					$col['default'] = '';
				}
			} else {
				$varType = 'mixed';
				if ($type === 'BLOB')
					$col['blob'] = 1;
				else if ($type === 'CLOB')
					$col['clob'] = 1;
			}

			// 如果是主键
			if ($field === $pkField) {
				$col['pk'] = 1;
				if ($pkAutoInc)
					$col['autoInc'] = 1;

				unset($col['default']);
			}

			$props[] = [$varType, "{$field}", $label];

			$cols[$field] = $col;

			$typeLength = strlen($varType);
			$fieldLength = strlen($field);
			if ($fieldLength > $fieldMaxLength)
				$fieldMaxLength = $fieldLength;
			if ($typeLength > $typeMaxLength)
				$typeMaxLength = $typeLength;
			// foreach@end
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

	public function isInt($dataType)
	{
		return isset($this->intTypes[$dataType]);
	}

	public function isFloat($dataType)
	{
		return isset($this->floatTypes[$dataType]);
	}

	public function isNumber($dataType)
	{
		return $this->isInt($dataType) || $this->isFloat($dataType);
	}

	public function isString($dataType)
	{
		return isset($this->stringType[$dataType]);
	}
}