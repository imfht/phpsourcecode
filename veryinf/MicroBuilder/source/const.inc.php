<?php
/**
 * 启动位置
 */

define('MB_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))) . '/');

define('APP_DEBUG', true);
define('APP_MODE', 'common');
define('THINK_PATH', MB_ROOT . 'source/ThinkPHP/');
define('COMMON_PATH', MB_ROOT . 'source/Common/');
define('STORAGE_TYPE', 'File');

define('LOG_PATH', MB_ROOT . 'source/Data/Logs/');

if(defined('IN_APP') && IN_APP === true) {
    define('APP_ROOT', MB_ROOT . 'm/');
    define('APP_PATH', APP_ROOT . 'code/');
    define('RUNTIME_PATH', MB_ROOT . 'source/Data/Runtime/App/');
} else {
    define('APP_ROOT', MB_ROOT . 'w/');
    define('APP_PATH', APP_ROOT . 'code/');
    define('RUNTIME_PATH', MB_ROOT . 'source/Data/Runtime/Web/');
}

require MB_ROOT . 'source/Conf/version.inc.php';

$_dir_ = dirname(dirname($_SERVER['SCRIPT_NAME']));
if($_dir_ == '/' || $_dir_ == '\\') {
    $_dir_ = '/';
} else {
    $_dir_ .= '/';
}
define('__SITE__', $_dir_);
$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
$_host_ = "{$scheme}://{$_SERVER['HTTP_HOST']}";
if($_SERVER['REQUEST_SCHEME'] == 'http' && $_SERVER['SERVER_PORT'] != '80') {
    $_host_ .= ":{$_SERVER['SERVER_PORT']}";
}
if($_SERVER['REQUEST_SCHEME'] == 'https' && $_SERVER['SERVER_PORT'] != '443') {
    $_host_ .= ":{$_SERVER['SERVER_PORT']}";
}
define('__HOST__', $_host_);
$cfgFile = MB_ROOT . 'source/Conf/config.inc.php';
if(!is_file($cfgFile)) {
    header('location: ../install.php');
    exit;
}

define('MSG_TYPE_SUCCESS', 1);
define('MSG_TYPE_INFO', 2);
define('MSG_TYPE_WARNING', 3);
define('MSG_TYPE_DANGER', 4);
define('TIMESTAMP', time());

define('IN_CONTAINER_MOBILE',   0 < stripos($_SERVER['HTTP_USER_AGENT'], 'mobile'));
define('IN_CONTAINER_WEIXIN',   0 < stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger'));
define('IN_CONTAINER_YIXIN',    0 < stripos($_SERVER['HTTP_USER_AGENT'], 'yixin'));
define('IN_CONTAINER_ALIPAY',   0 < stripos($_SERVER['HTTP_USER_AGENT'], 'alipay'));
