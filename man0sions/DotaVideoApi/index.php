<?php
header("Content-Type: text/html; charset=UTF-8");

// change the following paths if necessary
$yii=dirname(__FILE__).'/protected/framework/yiilite.php';

$config=dirname(__FILE__).'/protected/config/main.php';
define("ROOT_PATH",dirname(__FILE__));
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
if(!YII_DEBUG)
    ini_set('error_reporting', E_ALL ||~E_NOTICE);

require_once($yii);
Yii::createWebApplication($config)->run();
