<?php
// 编码
header("Content-type: text/html; charset=utf-8");
header('P3P: CP=CAO PSA OUR');

// 定义根目录
define('ROOT',		dirname(dirname(dirname(__FILE__))));	// 根目录
define('APP_ROOT',	dirname(dirname(__FILE__)));			// 应用根目
define('WEB_ROOT',	dirname(__FILE__));						// WEB根目
define('YII_ROOT',  APP_ROOT.'/protected');                 // YII框架目录
define('CDN_ROOT',  ROOT.'/project.cdn');

// debug
defined('YII_DEBUG') or define('YII_DEBUG',true);
error_reporting(E_ALL ^ E_NOTICE);

// 时区设置
date_default_timezone_set('Asia/Shanghai');

include ROOT.'/yiiframework/yii.php';
Yii::createWebApplication(ROOT.'/yiiframework/config/main.php')->run();
