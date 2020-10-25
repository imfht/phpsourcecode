<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm;


use Ke\Helper\DateHelper;
use mysql_xdevapi\Exception;

class Filter
{

	private static $pkAutoIncColumn = ['pk' => true, 'autoInc' => true, 'require' => false, 'unique' => true];

	private static $pkNotAutoIncColumn = ['pk' => true, 'autoInc' => false, 'require' => true, 'unique' => true];

	const INT = 1;

	const BIGINT = 2;

	const FLOAT = 3;

	const FLOAT_MAX = 12;

	const SERIALIZE_CONCAT = 'concat';

	const SERIALIZE_JSON = 'json';

	const SERIALIZE_PHP = 'php';

	const CONCAT_DELIMITER = ',';

	public function initColumn(
		string $field,
		array &$column,
		array &$groupColumns,
		bool $isPk = false,
		bool $isAutoInc = false)
	{
		if (!isset($groupColumns['default']))
			$groupColumns['default'] = [];
		$defaultData = &$groupColumns['default'];
		// 功能分组
		if (!empty($column['hidden']))
			$groupColumns['hidden'][$field] = true;
		if (!empty($column['require']))
			$groupColumns['require'][$field] = true;
		if (!empty($column['dummy']))
			$groupColumns['dummy'][$field] = true;

		// todo: !$isPk时，是否删除$column['pk']

		// 字段的默认值
		$default = null;
		// 是否定义了默认值
		$isDefineDefault = false;
		// 是否dummy字段
		$isDummy = !empty($columns['dummy']);
		// 数值类型
		$numeric = 0;

		if (isset($column['default'])) {
			$default = $column['default'];
			$isDefineDefault = true;
		} elseif (isset($defaultData[$field])) {
			$default = $defaultData[$field];
			$isDefineDefault = true;
		}
		if (!empty($column['numeric']))
			$numeric = (int)$column['numeric'];

		// 字段的过滤
		// options
		if (isset($column['options'])) {
			if (is_callable($column['options'])) {
				$column['options'] = call_user_func($column['options']);
			}
			if (empty($column['options']) || !is_array($column['options']))
				unset($column['options']);
			else {
				if ($default === null || !isset($column['options'][$default]))
					$default = array_keys($column['options'])[0];
			}
		}
		// 序列化设定
		// $column['serialize'] => 用于在Filter中快速确定是否进行序列化
		// $groupColumns['serialize'] => 用于在Model层面快速识别哪个字段为序列的字段
		if (isset($column['concat'])) {
			if (empty($column['concat']))
				$column['concat'] = ',';
			if (empty($default))
				$default = [];
			elseif (is_string($default))
				$default = explode(',', $default);
			elseif (!is_array($default))
				$default = (array)$default;
			$column['serialize'] = ['concat', $column['concat']];
			$column['array'] = true;
			$groupColumns['serialize'][$field] = true;
		} elseif (!empty($column['json'])) {
			$column['serialize'] = ['json', null];
			$groupColumns['serialize'][$field] = true;
		} elseif (!empty($column['php'])) {
			$column['serialize'] = ['php', null];
			$groupColumns['serialize'][$field] = true;
		} // 其他值类型
		else {
			// 到这里，首先就排除了序列化的可能性
			unset($column['serialize']);
			// 优先级，bool > timestamp > numeric > string
			if ($numeric > 0 ||
				!empty($column['int']) ||
				!empty($column['float']) ||
				!empty($column['bigint'])
			) {
				if (!empty($column['float'])) {
					$numeric = self::FLOAT;
					if (is_numeric($column['float']))
						$numeric += $column['float'];
				} elseif (!empty($column['bigint']))
					$numeric = self::BIGINT;
				elseif (!empty($column['int']))
					$numeric = self::INT;
				if ($numeric > self::FLOAT_MAX) // 9位小数
					$numeric = self::FLOAT_MAX;
				$column['numeric'] = $numeric;
			} else {
				// 到这里，也排除了是数值类型的可能性
				unset($column['numeric']);
			}
			// 这里就可以统一用过滤的方法，对默认值进行过滤处理了。
			if ($isDefineDefault)
				$default = $this->filterColumn($default, $column);
		}
		// 数据结构必须要写入，哪怕default = null
		$defaultData[$field] = $default;

		// 而字段本身default则根据这个字段是否定义了default来决定
		if ($isDefineDefault)
			$column['default'] = $default;

		if ($isPk) {
			$column += $isAutoInc ? self::$pkAutoIncColumn : self::$pkNotAutoIncColumn;
			if ($isAutoInc) {
				if (empty($column['numeric']) && empty($column['numeric']))
					$column['numeric'] = self::INT;
				unset($column['default'], $defaultData[$field]);
			} else {
//				if (empty($column['max']))
//					$column['max'] = 32;
				$defaultData[$field] = $default;
//				if (!isset($column['default']))
//					$column['default'] = '';
//				else
//					$column['default'] = trim($column['default']);
//				$defaultData[$field] = $column['default'];
			}
		}

		foreach ([Model::ON_CREATE, Model::ON_UPDATE, Model::ON_SAVE] as $process) {
			if (!isset($column[$process]))
				continue;
			$columnClone = $column;
			$columnCloneDefault = null;
			unset($columnClone[$process]);
			if (is_array($column[$process])) {
				// 不能合并default值
				unset($columnClone['default']);
				$columnClone = array_merge($columnClone, $column[$process]);
				if (isset($columnClone['default']))
					$columnCloneDefault = $columnClone['default'];
			} else {
				$columnCloneDefault = $column[$process];
			}
//			$columnCloneDefault = isset($columnClone['default']) ? $columnClone['default'] : null;
//			$columnClone['default'] = $this->filterColumn($columnCloneDefault, $columnClone);

			if ($process === Model::ON_UPDATE || $process === Model::ON_SAVE) {
				if (isset($columnCloneDefault))
					$groupColumns['update_data'][$field] = $columnCloneDefault;
				$groupColumns[Model::ON_UPDATE][$field] = $columnClone;
			}
			if ($process === Model::ON_CREATE || $process === Model::ON_SAVE) {
				if (isset($columnCloneDefault))
					$defaultData[$field] = $columnCloneDefault;
				$groupColumns[Model::ON_CREATE][$field] = $columnClone;
			}
		}
		return $column;
	}


	public function filterColumn($value, array $column, bool $isSerialize = true)
	{
		if (!empty($column['serialize'])) {
			if ($isSerialize)
				return $this->serialize($value, ...$column['serialize']);
			else
				return $value;
		}
		if (isset($column['filter']) && is_callable($column['filter']))
			$value = call_user_func($column['filter'], $value, $column);
		// options的值不再filter进行处理，而在validate进行验证处理
		// 剩下可能的值类型过滤
		if (!empty($column['bool'])) {
			$value = (bool)$value;
		} elseif (!empty($column['timestamp'])) {
			$value = $this->filterTimestamp($value, $column);
		} elseif (!empty($column['datetime'])) {
			$value = $this->filterDatetime($value, $column, $column['datetime']);
		} elseif (!empty($column['numeric'])) {
			$value = $this->filterNumeric($value, $column);
		} else {
			$value = $this->filterString($value, $column);
		}
		return $value;
	}

	public function filterTimestamp($value, array $column = null)
	{
		if (empty($value))
			$value = 0;
		elseif (is_numeric($value))
			$value = (int)$value;
		elseif (is_string($value)) {
			$value = strtotime($value);
			if ($value === false)
				$value = 0;
		} else
			$value = 0;
		return $value;
	}

	public function filterDatetime($value, array $column = null, string $datetimeType = 'datetime')
	{
		$default = DateHelper::DEFAULT_DATETIME_EMPTY_VALUE;
		$format = DateHelper::DEFAULT_DATETIME_FORMAT;
		if ($datetimeType === 'date') {
			$default = DateHelper::DEFAULT_DATE_EMPTY_VALUE;
			$format = DateHelper::DEFAULT_DATE_FORMAT;
		} else if ($datetimeType === 'time') {
			$default = DateHelper::DEFAULT_TIME_EMPTY_VALUE;
			$format = DateHelper::DEFAULT_TIME_FORMAT;
		}
		if (empty($value)) return $default;
		if ($value === 'now' || $value === 'current') {
			$value = DateHelper::withTime();
		} else if ($value instanceof DateHelper) {
			$value->setIgnoreTime(false);
		} else {
			try {
				$value = DateHelper::withTime($value);
			} catch (\Throwable $throwable) {
				return $default;
			}
		}
		return $value->format($format);
	}

	public function filterNumeric($value, array $column = null)
	{
		// todo: 整形的处理，需要增加unsigned类型的处理
		if ($column['numeric'] === self::INT) {
			$value = intval($value);
			// int类型
			if ($value === false) // 转型失败
				$value = 0;
		} elseif ($column['numeric'] === self::BIGINT) {
			// bigint，注意，因为从数据库中取出的bigint，php自动处理为字符串，所以这里也作为字符串处理
			if (!is_numeric($value))
				$value = '0';
			else
				$value = (string)$value;
		} elseif ($column['numeric'] >= self::FLOAT) {
			if (($value = (float)$value) === false) // 转型失败
				$value = 0;
			elseif ($column['numeric'] > self::FLOAT && $value !== 0)
				$value = round($value, $column['numeric'] - self::FLOAT);
		}
		return $value;
	}

	public function filterString($value, array $column = null)
	{
		if ($value === null) $value = ''; // null的可能性是比较高的
		elseif ($value === false) $value = '0';
		elseif ($value === true) $value = '1';
		else {
			$type = gettype($value);
			// 数组和资源类型，就不做字符转换的处理了。
			if ($type === KE_ARY || $type === KE_RES)
				$value = '';
			elseif ($type === KE_OBJ) {
				if (is_callable($value, '__toString'))
					$value = (string)$value;
				else
					$value = '';
			} else
				$value = trim($value);
		}
		if (!empty($column['trim']))
			$value = trim($value, $column['trim']);
		if (!empty($column['ltrim']))
			$value = ltrim($value, $column['ltrim']);
		if (!empty($column['rtrim']))
			$value = rtrim($value, $column['rtrim']);
		// 小写、大写只能是其中一种
		if (!empty($column['lower']))
			$value = mb_strtolower($value);
		elseif (!empty($column['upper']))
			$value = mb_strtoupper($value);
		// 移除html标签
		// 没定义的时候，默认强制删除html标签
		// 只有当html不为空，且不为entity的时候，才会保留html标签
		if (empty($column['html']))
			$value = strip_tags($value);
		elseif ($column['html'] === 'htmlentity')
			$value = htmlentities($value, ENT_COMPAT);
		return $value;
	}

	public function isSerializeValue($value, array &$matches = null)
	{
		return preg_match('#^(json|php|concat)(?:\[([^\[\]]))?:([\s\S]*)$#m', $value, $matches);
	}

	public function serialize($value, $scheme, $param = null)
	{
		$type = gettype($value);
		// 要先检查，如果data不是以下类型，则表示可以安全执行字符串检查
		if ($type !== KE_ARY && $type !== KE_OBJ && $type !== KE_RES) {
			// 如果检查本身已经带有序列化的标记，则不管，直接返回值
			if ($this->isSerializeValue($value)) {
				return $value;
			}
		}
		if ($type === KE_RES)
			$value = ''; // 资源类型不做序列化
		if ($scheme === self::SERIALIZE_JSON) {
			return 'json:' . json_encode($value);
		} elseif ($scheme === self::SERIALIZE_CONCAT) {
			if (empty($param) || !is_string($param))
				$param = self::CONCAT_DELIMITER;
			if (!is_array($value))
				$value = (array)$value;
			return 'concat[' . $param . ']:' . implode($param, $value);
		} elseif ($scheme === self::SERIALIZE_PHP) {
			return 'php:' . serialize($value);
		}
		return $value;
	}

	public function unSerialize($value)
	{
		if (empty($value))
			return $value;
		$type = gettype($value);
		if ($type === KE_ARY || $type === KE_OBJ || $type === KE_RES)
			return $value;
		if ($this->isSerializeValue($value, $matches)) {
			list(, $scheme, $param, $str) = $matches;
			if ($scheme === self::SERIALIZE_JSON) {
				return json_decode($str, true);
			} elseif ($scheme === self::SERIALIZE_CONCAT) {
				if (empty($param) || !is_string($param))
					$param = self::CONCAT_DELIMITER;
				return explode($param, $str);
			} elseif ($scheme === self::SERIALIZE_PHP) {
				return unserialize($str);
			}
		}
		return $value;
	}
}