<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

const KE_ASCII_0 = 48; // 1 => 49
const KE_ASCII_9 = 57;
const KE_ASCII_UPPER_A = 65;
const KE_ASCII_UPPER_Z = 90;
const KE_ASCII_LOWER_A = 97;
const KE_ASCII_LOWER_Z = 122;

if (!function_exists('camelcase')) {
	function camelcase($str, $tokens = ['-', '_', '.'], $first = false)
	{
		$result = ucwords(str_replace($tokens, ' ', strtolower($str)));
		$result = str_replace(' ', '', $result);
		if (isset($result[0]) && !$first) {
			$code = ord($result[0]);
			if ($code >= KE_ASCII_UPPER_A && $code <= KE_ASCII_UPPER_Z)
				$result[0] = strtolower($result[0]);
		}
		return $result;
	}
}

if (!function_exists('hyphenate')) {
	function hyphenate($str, $replace = '-', $first = false)
	{
		$str = preg_replace_callback('#([A-Z])#', function ($matches) use ($replace) {
			return $replace . strtolower($matches[1]);
		}, (string)$str);
		if (!$first)
			$str = ltrim($str, $replace);
		return $str;
	}
}

if (!function_exists('add_namespace')) {
	function add_namespace(string $class, string $namespace = null, bool $isStrictCase = false, string $spr = '\\'): string
	{
		$class = trim($class, KE_PATH_NOISE);
		$namespace = trim($namespace, KE_PATH_NOISE);
		$method = $isStrictCase ? 'strpos' : 'stripos';
		if (!empty($namespace) && !empty($class) && call_user_func($method, $class, $namespace . $spr) !== 0) {
			$class = $namespace . $spr . $class;
		}
		return $class;
	}
}

if (!function_exists('purge_namespace')) {
	function purge_namespace(string $class, string $namespace = null, bool $isStrictCase = false): string
	{
		$class = trim($class, KE_PATH_NOISE);
		$namespace = trim($namespace, KE_PATH_NOISE);
		$method = $isStrictCase ? 'strpos' : 'stripos';
		if (!empty($namespace) && !empty($class) && call_user_func($method, $class, $namespace . '\\') === 0) {
			$class = substr($class, strlen($namespace . '\\'));
		}
		return $class;
	}
}

if (!function_exists('path2class')) {
	function path2class(string $class, bool $isReplaceUnderScore = false): string
	{
		$class = str_replace(['.', '-'], '_', trim($class, KE_PATH_NOISE));
		$class = preg_replace_callback('#(^|[\\\\\/_])([a-z])?#', function ($matches) use ($isReplaceUnderScore) {
			if ($matches[1] === '/')
				$matches[1] = '\\';
			elseif ($isReplaceUnderScore && $matches[1] === '_') {
				$matches[1] = '';
			}
			$return = $matches[1];
			if (isset($matches[2]))
				$return .= strtoupper($matches[2]);
			return $return;
		}, $class);
		return $class;
	}
}

if (!function_exists('class2id')) {
	function class2id(string $class): string
	{
		$class = trim($class, KE_PATH_NOISE);
		$class = str_replace(['\\', '/'], '_', $class);
		return strtolower($class);
	}
}

if (!function_exists('str_len_cut')) {
	function str_len_cut($str, $length, $suffix = '...')
	{
		$strLen = mb_strlen($str);
		$suffixLen = mb_strlen($suffix);
		if ($strLen <= $length || $strLen <= $suffixLen)
			return $str;
		return (mb_substr($str, 0, $length - $suffixLen)) . $suffix;
	}
}

if (!function_exists('str_width_cut')) {
	function str_width_cut($str, $width, $suffix = '...')
	{
		$strWidth = mb_strwidth($str);
		$suffixWidth = mb_strwidth($suffix);
		if ($strWidth <= $width || $strWidth <= $suffixWidth)
			return $str;
		$newStr = mb_strimwidth($str, 0, $width, $suffix);
		return $newStr;
	}
}

if (!function_exists('str_summary')) {
	function str_summary($content, $len = 256)
	{
		$content = nl2br($content);
		$content = strip_tags($content);
		$content = str_replace('&nbsp;', ' ', $content);
		$content = trim($content);
		$content = preg_replace('/([\r\n]+|[\s]{2,})/i', ' ', $content);
		$content = str_len_cut($content, $len);
		return $content;
	}
}

if (!function_exists('float_precision')) {
	function float_precision($float, $precision = 2)
	{
		if (!is_numeric($float)) {
			return $float;
		}
		return sprintf("%01.{$precision}f", round($float, $precision));
	}
}

if (!function_exists('format_time')) {
	function format_time($time, $format = 'Y-m-d H:i:s')
	{
		if(!is_numeric($time) || empty($time))
			return null;
		return date($format, $time);
	}
}