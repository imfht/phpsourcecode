<?php 
// ModPHP 压缩包名称，如果设置，ModPHP 将从 ZIP 中加载内核
defined('MOD_ZIP') or define('MOD_ZIP', '');

require_once (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '').'mod/common/init.php'; //引入初始化程序

if(is_agent()){
	/** 浏览器安装 */
	require_once (MOD_ZIP ? 'zip://'.__DIR__.'/'.MOD_ZIP.'#' : '')."mod/common/install.php";
}else{
	/** 控制台安装 */
	$result = mod::install(array(
		"mod.database.type"=>"mysql", //数据库类型(mysql|sqlite)
		"mod.database.host"=>"localhost", //数据库主机地址
		"mod.database.name"=>"modphp", //数据库名
		"mod.database.port"=>3306, //数据库连接端口
		"mod.database.username"=>"root", //数据库登录用户
		"mod.database.password"=>"", //数据库登录密码
		"mod.database.prefix"=>"mod_", //数据表前缀
		"site.name"=>"", //网站名称
		"user_name"=>"", //管理员用户名，必填
		"user_password"=>"", //管理员密码，必填
		));
	print_r($result);
}