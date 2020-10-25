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

class Validator
{

//	public function buildErrorMessage(array $error): string
//	{
//		$message = null;
//		if (isset($error[0])) {
//			$message = array_shift($error);
//		} elseif (isset($error['msg'])) {
//			$message = $error['msg'];
//			unset($error['msg']);
//		}
//		return substitute($this->getMessage($message), $error);
//	}

	public function validateModelObject(Model $object, array &$data, $process = null, $isStrict = false)
	{
//		return $this;
//		var_dump($data);
		$filter = $object->getFilter();
		$shadow = [];
		$columns = $object->getColumns($process);
		$dbColumns = $object->dbColumns();
//		$groupColumns = $object->getGroupColumns();
		if ($isStrict) {
			if ($process === Model::ON_UPDATE)
				$shadow = $object->getShadowData();
		}
		$oldData = $data;

		foreach (array_keys($data) as $field) {
			$column = isset($columns[$field]) ? $columns[$field] : [];
			// 过滤值，同时必须更新$data
			$object[$field] = $data[$field] = $filter->filterColumn($data[$field], $column);
			//检查字段为必填项，但值为空（‘’）时，即报相关错误，而不是把空值变为0
			//@Easy
			if (isset($column['require']) && (bool)$column['require'] && strlen($oldData[$field]) < 1)
				$object[$field] = $data[$field] = $oldData[$field];
			$isRemove = false;
			if ($isStrict) {
				if (!empty($column['dummy']))
					$isRemove = true;
				if (!isset($dbColumns[$field]))
					$isRemove = true;
				if ($process === Model::ON_UPDATE) {
					if (isset($shadow[$field]) && equals($shadow[$field], $data[$field]))
						$isRemove = true;
				}
			}

			if ($isRemove) {
				unset($data[$field]);
				continue;
			}
			$error = $this->validateColumn($field, $data[$field], $column, $object, $process, $isStrict);

			if ($error !== false) {
				$object->setError($field, $error, false); // 不要覆盖已经存在的错误
			}
		}
		return $this;
	}

	public function validateColumn($field, $value, array $column, Model $obj = null, $process = null, $isStrict = false)
	{
		$require = isset($column['require']) && (bool)$column['require'];
		$allowEmpty = isset($column['empty']) ? (bool)$column['empty'] : !$require;
		$isEmail = isset($column['email']) && (bool)$column['email'] ? true : false;
		$error = false;

		if (!empty($column['datetime'])) {
			// datetime 优先处理
			$rs = $this->isValidDateTime($value, $field, $column['datetime'], $obj, $process);
			if ($rs === 0)
				$error = [Model::ERR_NOT_DATETIME];
			elseif ($rs === 2 && !$allowEmpty)
				$error = [Model::ERR_NOT_ALLOW_EMPTY];
		} elseif (!empty($column['numeric'])) {
			if (!is_numeric($value))
				$error = [Model::ERR_NOT_NUMERIC];
			elseif ($column['numeric'] >= 3 && !is_float($value))
				$error = [Model::ERR_NOT_FLOAT];
			// 同时判断
			elseif ((!empty($column['min']) && is_numeric($column['min'])) &&
				(!empty($column['max']) && is_numeric($column['max'])) &&
				($value < $column['min'] || $value > $column['max'])
			) {
				$error = [Model::ERR_NUMERIC_LESS_GREAT_THAN, 'min' => $column['min'], 'max' => $column['max']];
			} elseif (!empty($column['min']) && is_numeric($column['min']) && $value < $column['min'])
				$error = [Model::ERR_NUMERIC_LESS_THAN, 'min' => $column['min']];
			elseif (!empty($column['max']) && is_numeric($column['max']) && $value > $column['max'])
				$error = [Model::ERR_NUMERIC_GREET_THAN, 'max' => $column['max']];
		} else {
			$length = mb_strlen($value);
			if (!$allowEmpty && $length <= 0)
				$error = [Model::ERR_NOT_ALLOW_EMPTY];
//			elseif ($length > 0 || !$allowEmpty) {
			else {
				// 字符最小长度
				if ((!$allowEmpty || $length > 0) &&
					(!empty($column['min']) && is_numeric($column['min'])) &&
					(!empty($column['max']) && is_numeric($column['max'])) &&
					($length < $column['min'] || $length > $column['max'])
				) {
					$error = [Model::ERR_STR_LEN_LESS_GREAT_THAN, 'min' => $column['min'], 'max' => $column['max']];
				} elseif ((!$allowEmpty || $length > 0) &&
					(!empty($column['min']) && is_numeric($column['min'])) &&
					$length < $column['min']
				) {
					$error = [Model::ERR_STR_LEN_LESS_THAN, 'min' => $column['min']];
				} // 字符最大长度
				elseif ((!$allowEmpty || $length > 0) &&
					(!empty($column['max']) && is_numeric($column['max'])) &&
					$length > $column['max']
				) {
					$error = [Model::ERR_STR_LEN_GREET_THAN, 'max' => $column['max']];
				} // 邮箱
				elseif ((!$allowEmpty || $length > 0) && $isEmail && !$this->isEmail($value, $obj, $process))
					$error = [Model::ERR_NOT_EMAIL];
				elseif ((!$allowEmpty || $length > 0) && !empty($column['pattern']) && !$this->isMatch($value, $column['pattern'], $obj, $process)) {
					if (!empty($column['sample']))
						$error = [Model::ERR_NOT_MATCH_SAMPLE, 'sample' => $column['sample']];
					else
						$error = [Model::ERR_NOT_MATCH];
				} elseif ((!$allowEmpty || $length > 0) && !empty($column['options']) &&
					is_array($column['options']) &&
					!empty($column['inRange']) &&
					!isset($column['options'][$value])
				) {
					$error = [Model::ERR_NOT_IN_RANGE];
				} elseif ((!$allowEmpty || $length > 0) && $isStrict &&
					!empty($column['unique']) && isset($obj) &&
					!$this->isUnique($value, $field, $obj, $process)
				) {
					$error = [Model::ERR_DUPLICATE, 'value' => $value];
				} elseif ((!$allowEmpty || $length > 0) && !empty($column['equal']) && isset($obj) &&
					(!isset($obj[$column['equal']]) || !equals($obj[$column['equal']], $value))
				) {
					$error = [Model::ERR_NOT_EQUAL, 'equalLabel' => $obj->getLabel($column['equal'])];
				}
			}
		}
		return $error;
	}

	public function isEmail($value, Model $obj = null, $process = null)
	{
		return preg_match('/^[0-9a-z][a-z0-9\._-]{1,}@[a-z0-9-]{1,}[a-z0-9]\.[a-z\.]{1,}[a-z]$/i', $value);
	}

	public function isMatch($value, $pattern, Model $obj = null, $process = null)
	{
		if (!empty($pattern) && is_string($pattern)) {
			$pattern = '#' . $pattern . '#i';
			return preg_match($pattern, $value);
		}
		return true;
	}

	public function isUnique($value, $field, Model $obj = null, $process = null)
	{
		if ($obj->isMock()) return true; // 模拟数据，视为已经唯一
		$query = $obj->query(false)->in($field, $value);
		if ($obj->isExists())
			$query->notIn($obj->getReferenceData());
		return $query->count() > 0 ? false : true;
	}

	public function isValidDateTime($value, $field, string $datetimeType = 'datetime', Model $obj = null, $process = null)
	{
		$type = gettype($value);
		if ($type === KE_STR) {
			$value = trim($value);
			// 空字符串，是允许的值
			if (empty($value)) return 2;
			// now 也是允许值
			if ($value === 'now') return 1;
			$year = 0;
			$month = 0;
			$day = 0;
			$hour = 0;
			$minute = 0;
			$second = 0;
			$isMatch = false;
			if ($datetimeType === 'time') {
				// 作为时间的单独匹配，必须指定时期格式前的分界符、开头或者空格之类
				$isMatch = preg_match('#(?:[\s\t\b]|^)' . DateHelper::DEFAULT_TIME_PATTERN . '$#', $value, $matchDate);
				if (empty($matchDate)) $isMatch = false;
				else {
					$hour = intval($matchDate[1] ?? 0);
					$minute = intval($matchDate[2] ?? 0);
					$second = intval($matchDate[3] ?? 0);
				}
			} else {
				$isMatch = preg_match('#' . DateHelper::DEFAULT_DATETIME_PATTERN . '#', $value, $matchDate);
				$year = intval($matchDate[2] ?? 0);
				$month = intval($matchDate[3] ?? 0);
				$day = intval($matchDate[4] ?? 0);
				$hour = intval($matchDate[7] ?? 0);
				$minute = intval($matchDate[8] ?? 0);
				$second = intval($matchDate[9] ?? 0);
			}
			// 不匹配就完全属于格式不正确
			if (!$isMatch) return 0;

			$isEmptyDate = $year === 0 && $month === 0 && $day === 0;
			$isEmptyTime = $hour === 0 && $minute === 0 && $second === 0;

			if ($datetimeType === 'datetime') {
				return $isEmptyDate && $isEmptyTime ? 2 : 1;
			} elseif ($datetimeType === 'date') {
				return $isEmptyDate ? 2 : 1;
			} elseif ($datetimeType === 'time') {
				return $isEmptyTime ? 2 : 1;
			}
			// 剩下的交给最后的 return 来处理
		} elseif ($type === KE_INT) {
			// linux 时间戳，0 表示的是 1970-01-01 00:00:00
			// -1 表示减 1秒 1969-12-31 23:59:59 （还有时区问题，如果是+8时区，则+8小时）
			// 数值类型，强行认为他是有效的，因为数值类型不管怎么处理都是可以转换为有效的时间戳的
			if ($datetimeType === 'datetime') {
				$date = date('Y-m-d H:i:s', $value);
				return $date === DateHelper::DEFAULT_DATETIME_EMPTY_VALUE ? 2 : 1;
			} elseif ($datetimeType === 'date') {
				$date = date('Y-m-d', $value);
				return $date === DateHelper::DEFAULT_DATE_EMPTY_VALUE ? 2 : 1;
			} elseif ($datetimeType === 'time') {
				$date = date('H:i:s', $value);
				return $date === DateHelper::DEFAULT_DATE_EMPTY_VALUE ? 2 : 1;
			}
			// 剩下的交给最后的 return 来处理
		} elseif ($type === KE_OBJ) {
			// PHP 自身的 DateTime 方法，难道还不让他允许么？
			if ($value instanceof \DateTime) {
				return $this->isValidDateTime($value->format(DateHelper::DEFAULT_DATETIME_FORMAT), $field, $datetimeType, $obj, $process);
			}
			// 如果对象拥有 toDateTime 方法，我们也尝试执行一下，获取其值，然后在执行一次验证
			if (is_callable([$value, 'toDateTime']))
				return $this->isValidDateTime($value->toDateTime(), $field, $datetimeType, $obj, $process);
		}
		if (empty($value)) return 2;
		// 其他类型一律暂时当时无效的
		return 0;
	}
}