<?php
ini_set("display_errors", 1);
/* 指向public的上一级 */
define("APP_PATH",  realpath(dirname(__FILE__) . '/../'));

/* 加载系统运行环境*/
require APP_PATH . '/library/Alpaca/init/init.php';

/* 启动Alpaca */
Alpaca\Alpaca::app()->run();

