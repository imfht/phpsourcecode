<?php
//定义网站根目录
define('APP_ROOT',dirname(__FILE__));
//定义网站配置文件常量，该常量会在框架中使用
define('APP_CONFIG', APP_ROOT."/config/config.php");
//载入框架初始化程序
require APP_ROOT.'/CorePHP/init.php';
?>