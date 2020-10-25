<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

class Loader
{
	/* 路径映射 */
	public static $vendorMap = array(
// 	    'WxSDK' => __DIR__.DIRECTORY_SEPARATOR."WxSDK",//特殊定义路径映射
	);
	
	/**
	 * 自动加载器
	 */
	public static function autoload($class)
	{
		$file = self::findFile($class);
		if (file_exists($file)) {
			self::includeFile($file);
		}
	}
	
	/**
	 * 解析文件路径
	 */
	private static function findFile($class)
	{
	    $vendor = substr($class, 0, strpos($class, '\\')); // 需要导入的类 的顶级命名空间
		$vendorDir = isset(self::$vendorMap[$vendor]) ? self::$vendorMap[$vendor] : __DIR__.DIRECTORY_SEPARATOR.$vendor."/.."; // 文件基目录
		$filePath = substr($class, strlen($vendor)) . '.php'; // 文件相对路径
		return strtr($vendorDir . $filePath, '\\', DIRECTORY_SEPARATOR); // 文件标准路径
	}
	
	/**
	 * 引入文件
	 */
	private static function includeFile($file)
	{
		if (is_file($file)) {
			include $file;
		}
	}
}

spl_autoload_register('Loader::autoload'); // 注册自动加载方法
