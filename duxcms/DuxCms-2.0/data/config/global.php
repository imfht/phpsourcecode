<?php
$dir = dirname(__FILE__);
$files = array('performance.php','rewrite.php','upload.php','ver.php');
$db = include $dir.'/db.php';
$config = array (
	//默认模块
	'DEFAULT_APP' => 'home',
	'DEFAULT_CONTROLLER' => 'Index',
	'DEFAULT_ACTION' => 'index',
	'ERROR_URL' => '', //出错跳转地址
	'URL_BASE' => '', //设置网址域名
	//模板设置
	'TPL'=>array(
		'TPL_DEPR' => '/',
	),
	//数据库
	'DB'=>array(
		'default' => $db,
	),
	'CACHE' => array(
		'default' => array(
			'CACHE_TYPE' => 'FileCache',
			'CACHE_PATH' => ROOT_PATH . 'data/cache/',
			'GROUP' => 'db',
			'HASH_DEEP' => 0,
		),
	),
);
foreach ($files as $value) {
	$array = include $dir.'/'.$value;
	$config = array_merge($config,$array);
}
if(defined('ADMIN_STATUS')){
	$admin = include $dir.'/admin.php';
	$config = array_merge($config,$admin);
}
return $config;
