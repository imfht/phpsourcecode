<?php
// ModPHP 压缩包名称，如果设置，ModPHP 将从 ZIP 中加载内核
defined('MOD_ZIP') or define('MOD_ZIP', '');

/** PHP 内置服务器，使用 php -S 0.0.0.0:80 index.php 的方式开启服务器 */
if(PHP_SAPI == "cli-server"){
	$dir = realpath($_SERVER["DOCUMENT_ROOT"]).DIRECTORY_SEPARATOR;
	$file = explode("?", $_SERVER["REQUEST_URI"]);
	$file = lcfirst($dir.urldecode(substr($file[0], 1)));
	if(!file_exists($file)){
		$_SERVER["SCRIPT_FILENAME"] = $dir."index.php";
	}elseif($file != lcfirst(__FILE__) && $file != lcfirst(__DIR__.DIRECTORY_SEPARATOR)){
		return false;
	}
	unset($dir, $file);
}

require_once (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '').'mod/common/init.php'; //引入初始化程序

/**
 * 模板加载说明：
 * 默认地，ModPHP 会通过模板入口文件(index.php)来调用模板目录中对应的文件。
 * 但只有当访问的路径不是一个真实的文件或目录时，ModPHP 才会这么做。
 * 例如 http://localhost/something, ModPHP 的访问顺序是这样的：
 *   1. 尝试仿问站点根目录下的 something 文件或文件夹;
 *   2. 尝试访问模板目录下的 something 文件夹，如果存在这个文件夹，则尝试访问目录下的
 *      index.php, index.html, index.htm 文件，不存在这些文件则报告 403 错误;
 *   3. 尝试访问模板目录下的 something 文件;
 *   4. 尝试访问模板目录下的 something.php 文件;
 *   5. 尝试匹配模块记录的自定义链接;
 *   6. 尝试匹配伪静态规则;
 *   7. 没有查询到模板，报告 404 错误。
 */
