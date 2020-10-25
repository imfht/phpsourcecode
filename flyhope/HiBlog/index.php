<?php
//目录常量定义
define('ROOT_PATH', __DIR__ . '/');
define('APP_PATH', ROOT_PATH . '/application/');
define('TPL_PATH', APP_PATH . '/views/');
define('CONF_PATH', ROOT_PATH . 'conf/');

if(defined('SAE_TMP_PATH')) {
    define('TMP_PATH', SAE_TMP_PATH);
} else {
    define('TMP_PATH', ROOT_PATH . 'var/tmp/');
}

$app = new Yaf_Application(CONF_PATH . 'application.ini');
$app->bootstrap()->run();
