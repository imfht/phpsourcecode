<?php
//use Core\Route;
/*
此文件extend.php在cpApp.class.php中默认会加载，不再需要手动加载
用户自定义的函数，建议写在这里

下面的函数是框架的接口函数，
可自行实现功能，如果不需要，可以不去实现

注意：升级框架时，不要直接覆盖本文件,避免自定义函数丢失
*/


/* 
//模块执行结束之后，调用的接口函数
function cp_app_end(){	
	//在这里写代码实现你要实现的功能 
	$GLOBALS['_endTime'] = microtime(TRUE);// 记录结束运行时间
	function_exists('memory_get_usage') && $GLOBALS['_endUseMems'] = memory_get_usage();// 记录结束内存使用
	$total = $GLOBALS['_endTime'] - $GLOBALS['_beginTime'];   //计算差值
	$str_total = var_export($total, TRUE);
	if(substr_count($str_total,"E")){
		$float_total = floatval(substr($str_total,5));
		$total = $float_total/100000;
	}
	echo "运行时间 {$total}";
} 
*/



/* //自定义模板标签解析函数
function tpl_parse_ext($template){
	
} */



/* 
//自定义网址解析函数
function url_parse_ext(){
	isset($_GET['g']) && Route::$group=trim($_GET['g']);
	isset($_GET['m']) && Route::$module=trim($_GET['m']);
	isset($_GET['a']) && Route::$action=trim($_GET['a']);
} 
*/


//下面是用户自定义的函数

