<?php

/**
 * 一些常用的东西
 * 
 * @author ShuangYa
 * @package Blog
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\libs;
use \Sy;
use \sy\lib\db\Mysql;

class Common {
	const VERSION = '2.0';
	/**
	 * 选项相关
	 * @access public
	 * @param string $k
	 * @param string $v
	 */
	public static function option($k, $v = NULL) {
		if ($v === NULL) {
			$r = Mysql::i()->getOne("SELECT v FROM `#@__option` WHERE k = ?", [$k]);
			return stripslashes($r['v']);
		} else {
			$v = addslashes($v);
			$sql = "UPDATE `#@__option` SET v = ? WHERE k = ?";
			Mysql::i()->query($sql, [$v, $k]);
		}
	}
	/**
	* 自动建立多级目录
	* @param string $dir
	*/
	public static function mkdirs($dir) {
		if (is_dir($dir)) {
			return;
		}
		$updir = substr($dir, 0, strrpos($dir, '/'));
		if (!is_dir($updir)) {
			self::mkdirs($updir);
		}
		mkdir($dir, 0777);
	}
	/*
	* 使用curl抓取远程内容
	* @param string $url 地址
	* @param array $data 附加属性
	* @param array $data[header] 发送的HTTP头信息
	* @param array $data[cookie] 发送的Cookie
	* @param array $data[post] POST方式提交的数据
	* @return string
	*/
	public static function curlGet ($url, $data = []) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		if (substr(strtolower($url), 0, 5) === 'https') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		if (isset($data['header'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $data['header']);
		}
		if (isset($data['cookie'])) {
			curl_setopt($ch, CURLOPT_COOKIE, $data['cookie']);
		}
		if (isset($data['post'])) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);
		}
		$result=curl_exec($ch);
		@curl_close($ch);
		return $result;
	}
	/**
	* 获取随机字符串
	* @param int $length 长度
	* @param boolean $a 是否包括易混淆的字符串（用于验证码）
	* @return string
	*/
	public static function getRandStr($length, $a = TRUE) {
		if ($a) {
			$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		} else {
			$str = 'abcdefghjkmnpqrstwxyzABCDEFGHJKLMNPQRSTWXY3456789';
		}
		while (strlen($str) < $length) {
			$str .= $str;
		}
		$str = str_shuffle($str);
		return substr($str, 0, $length);
	}
	/**
	* 生成临时文件名
	* @param string $ext 扩展名
	* @return string
	*/
	public static function getTempName($ext = 'tmp') {
		$dir = Sy::$appDir . 'data/tmp';
		self::mkdirs($dir);
		return $dir . '/' . uniqid(self::getRandStr(5)). '.' .$ext;
	}
	public static function clearTempFiles() {
		$dir = Sy::$appDir . 'data/tmp';
		if (!is_dir($dir)) {
			return TRUE;
		}
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if ($file !== '.' && $file !== '..') {
				@unlink($dir . '/' . $file);
			}
		}
		@closedir($dh);
		return TRUE;
	}
}