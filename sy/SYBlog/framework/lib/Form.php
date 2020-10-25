<?php

/**
 * Form处理类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\lib;
use \Sy;

class Form {
	/**
	 * 格式验证
	 * @param string $str 待验证字符串
	 * @param string $type 验证类型
	 * @return boolean
	 */
	public static function valid($str, $type) {
		$func = 'self::is' . ucfirst($type);
		if (is_callable($func)) {
			return call_user_func($func, $str);
		} else {
			return TRUE;
		}
	}
	/**
	 * 验证字符串是否为EMail
	 * @access public
	 * @param string $str
	 * @return boolean
	 */
	public static function isEmail($str) {
		return preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str);
	}
	/**
	 * 验证字符串是否为合法的IP地址
	 * @access public
	 * @param string $ip
	 * @return boolean
	 */
	public static function isIpv4($ip) {
		$ip = explode('.', $ip);
		if (count($ip) !== 4) {
			return FALSE;
		}
		foreach ($ip as $one) {
			if ($one == '' || !is_int($one) || (int)$one > 255 || (int)$one < 0) {
				return FALSE;
			}
		}
		return TRUE;
	}
	public static function isIpv6($ip) {
		$collapsed = FALSE;
		$chunks = array_filter(preg_split('/(:{1,2})/', $ip, NULL, PREG_SPLIT_DELIM_CAPTURE));
		if (current($chunks) == ':' || end($chunks) == ':') {
			return FALSE;
		}
		while ($seg = array_pop($chunks)) {
			if ($seg[0] == ':') {
				if (strlen($seg) > 2) {
					return FALSE;
				}
				if ($seg == '::') {
					if ($collapsed) {
						return FALSE;
					}
					$collapsed = TRUE;
				}
			} elseif (preg_match("/[^0-9a-f]/i", $seg) || strlen($seg) > 4) {
				return FALSE;
			}
		}
		return $collapsed;
	}
	/**
	 * 验证字符串是否为日期
	 * @access public
	 * @param string $date
	 * @return boolean
	 */
	public static function isDate($date) {
		if (!preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date)) {
			return FALSE;
		}
		if (strtotime($date) === FALSE) {
			return FALSE;
		}
		return TRUE;
	}
}
