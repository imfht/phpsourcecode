<?php
/**
 * nightc-php-farmwork
 * auth:nightc http://www.nightc.com
 */

/**
 * the the dir
 */
define("AppRoot",Root."/app");//设置主题目录
define("CoreRoot",Root."/core");//设置class目录
define("EtcRoot",Root."/etc");//设置配置文件目录
define("TplRoot",Root."/tpl");//设置主题目录
define("LibRoot",Root."/lib");//设置class目录

/**
 * include the important thing
 */
include(CoreRoot."/function.php");//导入常用函数文件
include(CoreRoot."/class.php");//导入常用类文件

/**
 * define something
 */
define("App_StartTime",time());//设置启动时间
define('IS_CGI', (0 === strpos(PHP_SAPI, 'cgi') || false !== strpos(PHP_SAPI, 'fcgi')) ? 1 : 0);
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
define('IS_POST', $_SERVER['REQUEST_METHOD']=='POST' ? 1 : 0);
global $_REQ;
foreach ($_GET as $key => $value) {
    $_REQ['get.' . $key] = htmlspecialchars($value);
}
if (IS_POST) {
    foreach ($_POST as $key => $value) {
        $_REQ['post.' . $key] = htmlspecialchars($value);
    }
}
session_start();
/**
 * import the mvc class
 */
import('Model', CoreRoot);
import('Controller', CoreRoot);
import('View', CoreRoot);
App::run();