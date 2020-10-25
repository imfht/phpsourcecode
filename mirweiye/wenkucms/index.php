<?php
if (!is_file('./data/install.lock')) {
	header('Location: ./install.php');
	exit;
}
define('WEBROOT',dirname(__FILE__));
define('HOST','http://'.$_SERVER['HTTP_HOST']);
/* 应用名称 */  
define('APP_NAME', 'app');
/* 应用目录*/
define('APP_PATH', './app/');
/* 数据目录*/
define('MT_DATA_PATH', './data/');
/* 配置文件目录*/
define('CONF_PATH', MT_DATA_PATH . 'config/');
/* 数据目录*/
define('RUNTIME_PATH', MT_DATA_PATH . 'runtime/');
/* HTML静态文件目录*/
define('HTML_PATH', MT_DATA_PATH . 'html/');
/* DEBUG开关*/
define('APP_DEBUG', true);
require("./wkcms/wkcms.php");