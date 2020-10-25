<?php

/**
 * i18n基本类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=frameworkr&type=license
 */

namespace sy\base;
use Sy;
use \sy\base\SYException;

class i18n {
	//所有语言
	protected static $language = NULL;
	//当前语言
	protected static $now_language = NULL;
	/**
	 * 判断是否支持某一语言
	 * @access public
	 * @param $i
	 * @return mixed
	 */
	public static function getSupport($i) {
		$path = Sy::$appDir . 'i18n/' . $i . '.php';
		if (!is_file($path)) {
			return FALSE;
		}
		return $path;
	}
	/**
	 * 设置当前语言
	 * @access public
	 * @param string $i 语言
	 */
	public static function setLanguage($i) {
		if (static::$now_language === $i) {
			return;
		}
		if (FALSE === ($path = static::getSupport($i))) {
			throw new SYException('The language file not exists', '10013');
		}
		static::$language = require ($path);
	}
	/**
	 * 获取浏览器语言
	 * @access public
	 * @return string
	 */
	public static function getBrowserLanguage() {
		//Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3
		$accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$accept = explode(';', $accept);
		$accept_arr = [];
		$accept_first = '';
		foreach ($accept as $v) {
			if (substr($v, 0, 2) === 'q=') {
				$comma = strpos($v, ',');
				if ($comma === FALSE) {
					continue;
				}
				$q = intval(floatval(substr($v, 2, $comma - 2)) * 100);
				$l = trim(substr($v, $comma + 1));
				$accept_arr[$q] = explode(',', $l);
			} elseif (empty($accept_first)) {
				$accept_first = explode(',', trim($v));
			} else {
			}
		}
		krsort($accept_arr);
		if (!empty($accept_first)) {
			array_unshift($accept_arr, $accept_first);
		}
		return $accept_arr;
	}
	/**
	 * 获取默认语言
	 * 优先级：浏览器发送的Accept-Language >  Sy::$app->get('language');
	 * @return string
	 */
	public static function getDefaultLanguage() {
		$browser = static::getBrowserLanguage();
		//对IE特殊照顾
		$ie = ['zh-Hans-CN' => 'zh-CN', 'zh-Hans' => 'zh'];
		if (count($browser) !== 0) {
			$language = $browser[0][0];
		} elseif (Sy::$app->has('language')) {
			$language = Sy::$app->get('language');
		} else {
			$language = 'en-US';
		}
		if (isset($ie[$language])) {
			$language = $ie[$language];
		}
		return $language;
	}
	/**
	 * 设置语言为默认语言
	 * @access
	 */
	public static function setDefaultLanguage() {
		$browser = static::getBrowserLanguage();
		//对IE特殊照顾
		$ie = ['zh-Hans-CN' => 'zh-CN', 'zh-Hans' => 'zh'];
		if (count($browser) !== 0) {
			foreach ($browser as $v) {
				foreach ($v as $vv) {
					if (isset($ie[$vv])) {
						$vv = $ie[$vv];
					}
					if (static::getSupport($vv) !== FALSE) {
						static::setLanguage($vv);
						return;
					}
				}
			}
		}
		if (Sy::$app->has('language')) {
			$language =  Sy::$app->get('language');
			if (isset($ie[$language])) {
				$language = $ie[$language];
			}
			if (static::getSupport($language)) {
				static::setLanguage($language);
				return;
			}
		}
		throw new SYException('The language file not exists', '10014');
	}
	/**
	 * 获得文字
	 * @access public
	 * @param string $key
	 * @param array $params 自动替换的参数
	 * @return string
	 */
	public static function get($key, $params = NULL) {
		if (static::$language === NULL) {
			static::setDefaultLanguage();
		}
		if (!isset(static::$language[$key])) {
			return '';
		}
		$r = static::$language[$key];
		$params = (array )$params;
		foreach ($params as $k => $v) {
			$r = str_replace('{{' . $k . '}}', $v, $r);
		}
		return $r;
	}
}
