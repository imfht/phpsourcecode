<?php

/*
 *  @author myf
 *  @date 2014-11-13 12:15:02
 *  @Description myfmvc核心类库
 *  @codeEncode UTF8
 *  @web http://www.minyifei.cn
 */
date_default_timezone_set('PRC');
header("Content-Type:text/html; charset=utf-8");
session_start();
//项目跟路径
define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
//项目相对目录
define("SITE_PATH", dirname($_SERVER['SCRIPT_NAME']));
//系统配置路径
define('APP_SYS_PATH', dirname(__FILE__));
define('APP_SITE_PATH', dirname(dirname(__FILE__)));
//引用全局函数
require_once(APP_SYS_PATH."/functions.php");
//临时文件目录
define("TMP_PATH",dirname(APP_SYS_PATH).'/runtime');
//临时缓存文件目录
define("CACHE_PATH",dirname(APP_SYS_PATH).'/runtime/cache');
//日志路径
define("LOG_PATH", TMP_PATH.'/logs/');
if(!is_dir(LOG_PATH)){
    createFolders(LOG_PATH);
}
//定义配置文件
$_config = array();
$configFile = dirname(APP_SYS_PATH)."/config.php";
if(file_exists($configFile)){
    $_config = require_once ($configFile);
}
//引用smarty模板引擎
require_once (APP_SYS_PATH."/smt/Smarty.class.php");
//系统controller
require_once(APP_SYS_PATH."/controller.php");
//数据库基层类库
require_once(APP_SYS_PATH."/DB.php");
require_once(APP_SYS_PATH."/Model.php");
$namespaces = C("namespaces");
spl_autoload_register("loader");
//读取路由解析器
$route = getMvcRoute();
//控制器
$myfController = $route["c"];
//执行方法
$myfAction = $route["a"];
$myfControllerName = ucfirst($myfController)."Controller";
//控制器文件
$myfControllerFile = APP_PATH."/app/controller/".$myfControllerName.".php";
if(file_exists($myfControllerFile)){
    require_once ($myfControllerFile);
    $myfC = new $myfControllerName();
    //初始化方法
    $myfC->_sys_init_action($myfController);
    //执行前置方法
    $myfC->_before_action();
    //执行当前方法
    $myfC->{$myfAction}();
    //执行后置方法
    $myfC->_after_action();
}else{
    echo "error 404";
}