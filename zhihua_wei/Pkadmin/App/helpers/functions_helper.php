<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/02 0031
 * Time: 上午 9:16
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power:  公共函数库
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('password')) {
	/**
	 * 函数：加密
	 * @param string $password 未加密的密码
	 * @return string 加密后的密码
	 * @todo 加密技巧：http://blog.csdn.net/zhihua_w/article/details/52754732
	 */
	function password($password) {
		return md5(crypt('P' . $password . 'K', 'PK'));
	}

}

if (!function_exists('random')) {
	/**
	 * 函数：随机字符
	 * @param number $length 长度
	 * @param string $type 类型
	 * @param number $convert 转换大小写
	 * @return string 随机字符串
	 */
	function random($length = 6, $type = 'string', $convert = 0) {
		$config = array('number' => '1234567890', 'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789', 'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

		if (!isset($config[$type]))
			$type = 'string';
		$string = $config[$type];

		$code = '';
		$strlen = strlen($string) - 1;
		for ($i = 0; $i < $length; $i++) {
			$code .= $string{mt_rand(0, $strlen)};
		}
		if (!empty($convert)) {
			$code = ($convert > 0) ? strtoupper($code) : strtolower($code);
		}
		return $code;
	}

}
