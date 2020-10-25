<?php
# Minimum PHP version: 5.5
if (version_compare(phpversion(),'5.5')<0) {
	exit('Minimum PHP version: 5.5');
}
error_reporting(E_ALL);

# DEV环境计算运行时间
if (defined('APP_MODE') && APP_MODE=='DEV'){
	$GLOBALS['core'] = array();
	$GLOBALS['core']['_begin_time'] = microtime(true);
	$GLOBALS['core']['_begin_memory'] = memory_get_usage();
}

define('YSF_VERSION','v0.9.0-beta 20160326');
define('YSF_AUTHOR','moobusy@qq.com');
define('YSF_PATH',dirname(__FILE__));
define('PHP_MODE',PHP_SAPI=='cli'? 'cli' : 'web');
define('TIME',$_SERVER['REQUEST_TIME']);

if (PHP_MODE=='web') {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?'https://':'http://';
	$host = $_SERVER['HTTP_HOST'];
	$path = explode('/', $_SERVER['PHP_SELF']);
	unset($path[count($path)-1]);
	$path = implode('/', $path) . '/';
	define('SITE_URL',  $protocol . $host . $path);
	unset($protocol, $host, $path);
}else{
	define('SITE_URL','');
}


include_once YSF_PATH . '/Function/function.php';
include_once YSF_PATH . '/Library/Ysf.class.php';
spl_autoload_register('ysf_auto_load',true,true);
\Ysf\Ysf::init();