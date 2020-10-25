<?php

/**
 * 安全相关类
 * 包括内容：XSS，CSRF，密码安全
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
use \sy\lib\Cookie;
use \sy\base\SYException;

class Security {
	protected static $csrf_config = ['tokenName' => '_csrf_token', 'cookieName' => '_csrf_token'];
	protected static $csrf_hash = NULL;
	/**
	 * csrf验证
	 * @param boolean $show_error 验证不通过时，是否直接报错
	 * @return boolean
	 */
	public static function csrfVerify($show_error = TRUE) {
		//仅POST需要验证csrf
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			static::csrfSetCookie();
			return TRUE;
		}
		if (!isset($_POST[static::$csrf_config['tokenName']]) || Cookie::get(static::$csrf_config['cookieName']) === NULL || ($_POST[static::$csrf_config['tokenName']] !== Cookie::get(static::$csrf_config['cookieName']))) {
			if ($show_error) {
				header(Sy::getHttpStatus('403'));
				exit;
			} else {
				return FALSE;
			}
		}
		unset($_POST[static::$csrf_config['tokenName']]);
		return TRUE;
	}
	/**
	 * 生成/获取csrf_hash
	 * @return string
	 */
	public static function csrfGetHash() {
		if (static::$csrf_hash === NULL) {
			$cookie_hash = Cookie::get(static::$csrf_config['cookieName']);
			if ($cookie_hash !== NULL && preg_match('/^[0-9a-f]{32}$/iS', $cookie_hash)) {
				return static::$csrf_hash = $cookie_hash;
			}
			static::$csrf_hash = md5(uniqid(microtime(), TRUE));
		}
		return static::$csrf_hash;
	}
	/**
	 * 设置CSRF-Cookie
	 * @access public
	 */
	public static function csrfSetCookie() {
		$param = ['name' => static::$csrf_config['cookieName'], 'value' => static::csrfGetHash($requestId), 'httponly' => TRUE];
		Cookie::set($param);
	}
	/**
	 * 进行可逆加密
	 * @access public
	 * @param string $string 需要加密/解密的字符串
	 * @param string $operation 类型，加密为ENCODE，解密为DECODE
	 * @param string $key 加密/解密Key。默认从config读取
	 * @param int $expire 过期时间，单位：秒。0为不过期
	 * @return string
	 */
	public static function securityCode($string, $operation = 'ENCODE', $key = '', $expire = 0) {
		$ckey_length = 4;
		$key = md5($key ? $key : Sy::$app->get('cookieKey'));
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
		$cryptkey = $keya . md5($keya . $keyc);
		$key_length = strlen($cryptkey);
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expire ? $expire + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
		$string_length = strlen($string);
		$result = '';
		$box = range(0, 255);
		$rndkey = array();
		for ($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}
		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for ($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if ($operation == 'DECODE') {
			if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc . str_replace('=', '', base64_encode($result));
		}
	}
	/**
	 * 密码加密
	 * 采用hash_hmac，防止对框架的针对性破解
	 * @access public
	 * @param string $password 密码
	 * @param string $key 密钥
	 * @return string
	 */
	public static function password($password, $key = '') {
		if (empty($key)) {
			$key = Sy::$app->get('securityKey');
		}
		return hash_hmac('md5', $password, $key);
	}
}
