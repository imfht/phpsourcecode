<?php
/**
 * 常用函数库
 */

/**
 * des 导入类文件
 * param $name类名 $root 类的根目录 $ext 类的后缀名
 * return 是否正确导入
 */
function import($class_name, $root = LibRoot, $ext=".class.php") {
	$lib_path = $root."/".$class_name.$ext;
	if(file_exists($lib_path)){
		include_once($lib_path);
		return true;
	}
	return false;
}
function I($k) {
	return isset($GLOBALS['_REQ'][$k])?$GLOBALS['_REQ'][$k]:"";
}