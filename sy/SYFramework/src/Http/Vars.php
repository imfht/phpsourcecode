<?php
/**
 * Http vars
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Http;

use Sy\App;

class Vars {
	/**
	 * 获取HTTP状态文字
	 * @access public
	 * @param string $code 状态码
	 */
	public static function getStatus($code) {
		static $status = null;
		if ($status === NULL) {
			$status = require(SY_PATH . 'Data/httpStatus.php');
		}
		$version = ((isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') ? '1.0' : '1.1');
		if (isset($status[$code])) {
			$statusText = $status[$code];
			return "HTTP/$version $code $statusText";
		} else {
			return "HTTP/$version $code";
		}
	}
	/**
	 * 发送Content-type的header，也就是mimeType
	 * @access public
	 * @param string $type 可为文件扩展名，或者Content-type的值
	 */
	public static function setMimeType($type) {
		$mimeType = static::getMimeType($type);
		if ($mimeType === NULL) {
			$mimeType = $type;
		}
		$header = $mimeType . ';';
		if (in_array($type, ['js', 'json', 'atom', 'rss', 'xhtml'], TRUE) || substr($mimeType, 0, 5) === 'text/') {
			$header .= ' charset=' . App::$config->get('charset');
		}
		header('Content-type:' . $header);
	}
	/**
	 * 获取扩展名对应的mimeType
	 * @access public
	 * @param string $ext
	 * @return string
	 */
	public static function getMimeType($ext) {
		static $type = null;
		if ($type === NULL) {
			$type = require(SY_PATH . 'Data/mimeTypes.php');
		}
		$ext = strtolower($ext);
		return isset($type[$ext]) ? ($type[$ext]) : null;
	}
}