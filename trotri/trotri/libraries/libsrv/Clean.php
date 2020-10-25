<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libsrv;

use tfc\util\String;

/**
 * Clean class file
 * 表单数据清理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Clean.php 1 2013-03-29 16:48:06Z huan.song $
 * @package libsrv
 * @since 1.0
 */
class Clean
{
	/**
	 * 基于配置清理表单提交的数据
	 * <pre>
	 * 一.清理规则：
	 * $rules = array(
	 *	 'user_loginname' => 'trim',
	 *	 'user_interest' => array($foo, 'explode')
	 * );
	 * 参数：
	 * $attributes = array(
	 *	 'user_loginname' => '  abcdefghi  ',
	 *	 'user_interest' => ' 1, 2'
	 * );
	 * 结果：
	 * $result = array(
	 *	 'user_loginname' => 'abcdefghi',
	 *	 'user_interest' => array(1, 2)
	 * );
	 *
	 * 二.清理规则：
	 * $rules = array(
	 *	 'user_password' => 'md5',
	 *	 'user_interest' => array($foo, 'implode')
	 * );
	 * 参数：
	 * $attributes = array(
	 *	 'user_password' => '  1234  ',
	 *	 'user_interest' => array(1, 2)
	 * );
	 * 结果：
	 * $result = array(
	 *	 'user_loginname' => '81dc9bdb52d04dc20036dbd8313ed055',
	 *	 'user_interest' => '1,2'
	 * );
	 * </pre>
	 * @param array $rules
	 * @param array $attributes
	 * @return array
	 */
	public static function rules(array $rules, array $attributes)
	{
		foreach ($rules as $columnName => $funcName) {
			if (isset($attributes[$columnName])) {
				$attributes[$columnName] = call_user_func($funcName, $attributes[$columnName]);
			}
		}

		return $attributes;
	}

	/**
	 * 清理正整数数据，如果为负数则返回false
	 * @param integer|array $value
	 * @return mixed
	 */
	public static function positiveInteger($value)
	{
		if (is_array($value)) {
			$value = array_map('intval', $value);
			foreach ($value as $_v) {
				if ($_v <= 0) {
					return false;
				}
			}
		}
		else {
			if (($value = (int) $value) <= 0) {
				return false;
			}
		}

		return $value;
	}

	/**
	 * 处理SQL中用','拼接的查询条件
	 * @param string $value
	 * @return string
	 */
	public static function sqlPositiveInteger($value)
	{
		$value = self::positiveInteger(explode(',', $value));
		if ($value) {
			$value = implode(',', array_unique($value));
			return $value;
		}

		return false;
	}

	/**
	 * 清理字段，除去左右空格，并且escapeXss
	 * @param string $value
	 * @return string
	 */
	public static function cleanXss($value)
	{
		return String::escapeXss($value);
	}

	/**
	 * 将IPv4转成长整型
	 * @param string $value
	 * @return integer
	 */
	public static function ip2long($value)
	{
		return ip2long($value);
	}

	/**
	 * 用','拼接字符串
	 * @param array $value
	 * @return string
	 */
	public static function join($value)
	{
		if (is_array($value)) {
			$value = implode(',', $value);
		}

		return $value;
	}

	/**
	 * trim一维数组中每个元素，如果不是数组，转换为数组
	 * @param array $value
	 * @return array
	 */
	public static function trims($value)
	{
		$value = (array) $value;
		$value = array_map('trim', $value);
		return $value;
	}

	/**
	 * int一维数组中每个元素，如果不是数组，转换为数组
	 * @param array $value
	 * @return array
	 */
	public static function intvals($value)
	{
		$value = (array) $value;
		$value = array_map('intval', $value);
		return $value;
	}
}
