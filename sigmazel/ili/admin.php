<?php
//版权所有(C) 2014 www.ilinei.com
define('DEBUG', true);
define('ADMIN_SCRIPT', 'admin.php?r=');

//加载启动文件
require_once './source/boot.php';

//检查是否安装
if(!$config['installed']){
	header('location:_install/install.php');
	exit(0);
}

try{
	$db->connect();
	
	//全局管理脚本变量
	$ADMIN_SCRIPT = ADMIN_SCRIPT;

	//需要什么你就创建什么，系统会自动加载
	$_setting = new \admin\model\_setting();
	$_menu = new \admin\model\_menu();

	//检查IP
	if(!$_setting->check_allow_ip()) throw new Exception($GLOBALS['lang']['error.ip.limit']);
	
	//获取调度
	$runtime = new \ilinei\runtime();
	$dispatches = $runtime->dispatch();

	//检查调度权限
	$dispatches = $_menu->check_dispatches($dispatches);

	//无权限时抛错，记得抛
	if(!$dispatches) throw new Exception('Access Denied!');
	
	//开始执行
	$runtime->execute();
	
	exit(0);
}catch (Exception $exception){
	show_exception($exception);
}
?>