<?php

/*************************************************************
* 框架支持文件
* @author 暮雨秋晨
* @copyright 2014
*************************************************************/

/**
 * 自动加载函数
 */
function autoload($name)
{
    $name = trim($name);
    if (is_file($file = FRAMEWORK_CORE . $name . '.php') || is_file($file =
        FRAMEWORK_BASE . $name . '.php')) {
        require_once $file;
    } elseif (is_file($file = FRAMEWORK_CORE . 'C' . DS . $name . '.php') || is_file
    ($file = FRAMEWORK_CORE . 'M' . DS . $name . '.php') || is_file($file =
        FRAMEWORK_CORE . 'V' . DS . $name . '.php') || is_file($file = FRAMEWORK_EXTS .
        strtolower($name) . '.class.php')) {
        require_once $file;
    } else {
        throw new exception('[FatalError] The class file [' . $name . '] does not exist');
    }
}

/**
 * @abstract 异常捕获函数
 */
function exception_handler($e)
{
    die('Mesg: ' . $e->getMessage() . '<br />' . 'Line: ' . $e->getLine() . '<br />' .
        'File: ' . $e->getFile());
}

set_exception_handler('exception_handler');
spl_autoload_register('autoload');

/**
 * 重置系统全局变量
 */
$_REQUEST = $_GET + $_POST + $_COOKIE;
if (isset($_SERVER['PATH_INFO']) && $_SERVER['PHP_SELF'] === $_SERVER['SCRIPT_NAME'] .
    $_SERVER['PATH_INFO']) {
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
}

/**
 * 全局防注入
 */
if (isset($_GET) && !empty($_GET)) {
    $_GET = DataEscape($_GET);
}
if (isset($_POST) && !empty($_POST)) {
    $_POST = DataEscape($_POST);
}
if (isset($_COOKIE) && !empty($_COOKIE)) {
    $_COOKIE = DataEscape($_COOKIE);
}
