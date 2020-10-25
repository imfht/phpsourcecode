<?php
//版权所有(C) 2014 www.ilinei.com
define('DEBUG', true);

//加载启动文件
require_once './source/boot.php';

//检查是否安装
if(!$config['installed']){
	header('location:_install/install.php');
	exit(0);
}

try{
	//全局皮肤变量
	$THEME = $setting['SiteTheme'].'/{$THEME}';

	//需要什么就创建，自动加载
	$_setting = new \admin\model\_setting();
	
	//检查IP
	if($_setting->check_limit_ip()) throw new Exception($GLOBALS['lang']['error.ip.limit']);
	if($_setting->check_closed()) throw new Exception($setting['SiteClosedReason']);

	//获取调度
	$runtime = new \ilinei\runtime();
	$dispatches = $runtime->dispatch();
	
	//以模板文件夹作为模块
	$dispatches['module'] != 'misc' && $dispatches['module'] = strtr($setting['SiteTheme'], '/', '\\');

	//如果模板站，直接显示
	if($dispatches['page']){
	    //默认打开数据库
	    $db->connect();
	    
	    //创建解析器，就用我们团队名啦。
	    $_ilinei = new \admin\model\_ilinei();
	    
	    $pages = $_ilinei->load($dispatches['module']);
	    !$pages[$dispatches['page']] && $dispatches['page'] = 'index';
	    
	    include_once view("/{$setting[SiteTheme]}/page/{$dispatches[page]}");
    }else{
	    //其它直接执行，是的。
	    $runtime->execute();
    }
    
	exit(0);
}catch (Exception $exception){
	show_exception($exception);
}
?>