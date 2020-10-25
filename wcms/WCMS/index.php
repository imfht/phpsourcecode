<?php
session_start();
@header("Content-type: text/html; charset=utf-8");

//关闭错误提示 0关闭  1开启
ini_set('display_errors', 1);

error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT); //php5.4报错 E_STRICT

//配置时区
date_default_timezone_set('Asia/Shanghai');
define('ROOT', realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('APP', './app/');
//配置加载路径
set_include_path(implode(PATH_SEPARATOR, array(
        realpath('./lib'),
        realpath('./config'),
        realpath('./lang'),
        get_include_path())));


//引入多语言
if (! isset($_COOKIE['lang'])) {
    setcookie("lang", "cn", time() + 3600 * 10, "/");
}

//是否捕获fatal error:non-object错误
define('NONOBJECT', 1);
require_once 'App.php';
$app = App::getInstance();
$app->run();
