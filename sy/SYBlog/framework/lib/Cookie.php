<?php

/**
 * Cookie类
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
use \sy\base\SYException;

class Cookie {
	/**
	 * 设置Cookie
	 * @access public
	 * @param array $param
	 * @param string $param[name] 名称
	 * @param string $param[value] 内容
	 * @param int $param[expire] 过期时间，-1为失效，0为SESSION，不传递为从config读取，其他为当前时间+$expire
	 * @param string $param[path] 若不传递，则从config读取
	 * @param string $param[domain] 若不传递，则从config读取
	 * @param boolean $param[https] 是否仅https传递，默认根据当前URL设置
	 * @param boolean $param[httponly] 是否为httponly
	 */
	public static function set($param) {
		$name = Sy::$app->get('cookie.prefix') . $param['name'];
		//处理过期时间
		if (!isset($param['expire'])) {
			$expire = time() +Sy::$app->get('cookie.expire');
		} elseif ($param['expire'] === -1) {
			$expire = time() - 3600;
		} elseif ($param['expire'] === 0) {
			$expire = 0;
		} else {
			$expire = time() + $param['expire'];
		}
		//其他参数的处理
		!isset($param['path']) && $param['path'] = Sy::$app->get('cookie.path');
		!isset($param['domain']) && $param['domain'] = Sy::$app->get('cookie.domain');
		!isset($param['httponly']) && $param['httponly'] = FALSE;
		//HTTPS
		if (!isset($param['https'])) {
			if ($_SERVER['HTTPS'] === 'on') {
				$param['https'] = TRUE;
			} else {
				$param['https'] = FALSE;
			}
		}
		//设置
		setcookie($name, $param['value'], $expire, $param['path'], $param['domain'], $param['https'], $param['httponly']);
	}
	/**
	 * 获取Cookie
	 * @access public
	 * @param string $name
	 * @return string
	 */
	public static function get($name) {
		$name = Sy::$app->get('cookie.prefix') . $name;
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL;
	}
	/**
	 * Cookie是否存在
	 * @access public
	 * @param string $name
	 * @return string
	 */
	public static function exists($name) {
		$name =Sy::$app->get('cookie.prefix') . $name;
		return isset($_COOKIE[$name]);
	}
	/**
	 * 安全的设置Cookie（防止篡改）
	 * @param int $type 类型
	 *  1.签名方式，仅防止篡改，不防止任意读取
	 *  2.securityCode方式，防止篡改与读取，还用于保证数据完整性
	 * @param array $cookie
	 */
	public static function sSet($type, $cookie) {
		if ($type === 1) {
			$sign = md5($cookie['value'] . Sy::$app->get('cookieKey'));
			self::set($cookie);
			$cookie['name'] .= '_sign';
			$cookie['value'] = $sign;
			self::set($cookie);
		} else {
			$cookie['value'] = Security::securityCode($cookie['value'], 'ENCODE');
			self::set($cookie);
		}
	}
	/**
	 * 安全的获取Cookie
	 * @param string $name
	 * @param int $type 如不输入，将自动检测
	 */
	public static function sGet($name, $type = 0) {
		$v = self::get($name);
		if ($v === NULL) {
			return NULL;
		}
		if ($type === 1 || ($type === 0 && self::exists($name . '_sign'))) {
			$sign = md5($v . Sy::$app->get('cookieKey'));
			if ($sign === self::get($name . '_sign')) {
				return $v;
			} else {
				return NULL;
			}
		}
		if ($type === 2 || $type === 0) {
			$vv = Security::securityCode($v, 'DECODE');
			if (is_string($vv)) {
				return $vv;
			}
			if ($vv === NULL && $type === 2) {
				return NULL;
			}
		}
		return NULL;
	}
}
