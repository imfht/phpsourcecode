<?php

// 定义根目录
define('ROOT',		dirname(dirname(dirname(__FILE__))));	// 根目录
define('APP_ROOT',	dirname(dirname(__FILE__)));			// 应用根目
define('WEB_ROOT',	dirname(__FILE__));						// WEB根目
define('YII_ROOT',  APP_ROOT.'/protected');                 // YII框架目录
define('CDN_ROOT',  ROOT.'/project.cdn');                   // CDN目录

date_default_timezone_set('Asia/Shanghai');

$config = ROOT.'/yiiframework/config/main.php';
require_once(ROOT.'/yiiframework/yiic.php');
