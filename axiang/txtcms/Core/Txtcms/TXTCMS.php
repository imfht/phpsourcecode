<?php
//TXTCMS 入口文件
define('INI_TXTCMS',true);
//定义系统目录
defined('TXTCMS_PATH') or define('TXTCMS_PATH', str_replace('\\','/',dirname(__FILE__)).'/');
defined('APP_PATH')	 or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
//系统开始运行时间
define('_START_TIME', microtime());
//加载系统配置
require TXTCMS_PATH.'Common/run.php';