<?php
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    error_reporting(E_COMPILE_ERROR|E_ERROR);
    //die('Not Found');
    $config=dirname(__FILE__).'/system/core/admin/config/main.php';
} else {
    error_reporting(0);
    defined('YII_DEBUG') or define('YII_DEBUG', false);
    $config = dirname(__FILE__).'/system/core/admin/config/main.php';
}
require_once('./common.php');