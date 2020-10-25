<?php

/**
 * @since	    version 3.1.0
 * @author	    Fqb <fan@dayrui.com>
 * @license     http://www.dayrui.com/license
 * @copyright   Copyright (c) 2011 - 9999, Dayrui.Com, Inc.
 */

header('Content-Type: text/html; charset=utf-8');

// 显示错误提示
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);
function_exists('ini_set') && ini_set('display_errors', TRUE);
function_exists('ini_set') && ini_set('memory_limit', '1024M');

// 查询执行超时时间
function_exists('set_time_limit') && set_time_limit(100);

// 系统核心程序目录,支持自定义路径和改名
define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'diy'.DIRECTORY_SEPARATOR);

// web网站目录,表示index.php文件的目录
define('WEBPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

// web网站目录,表示index.php文件的目录
define('CACHEPATH', WEBPATH.'cache'.DIRECTORY_SEPARATOR);


// 该文件的名称
!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

if (PHP_SAPI === 'cli' || defined('STDIN')) {
    unset($_GET);
    $_GET['c'] = 'cron';
    $_GET['m'] = 'index';
    chdir(dirname(__FILE__));
}

// 执行主程序
require FCPATH.'Init.php';