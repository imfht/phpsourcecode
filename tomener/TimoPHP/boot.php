<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

date_default_timezone_set('Asia/Chongqing');

//检查环境版本
version_compare(PHP_VERSION, '5.6.0', '>=') || die('requires PHP 5.6.0+ Please upgrade!');

define('VERSION', '1.7.6');

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

defined('ROOT_PATH') || die('[TimoPHP] undefined ROOT_PATH constant.');

defined('APP_DIR_PATH') || define('APP_DIR_PATH', ROOT_PATH . 'app' . DS);

define('FRAME_PATH', __DIR__ . DS);

define('LIBRARY_PATH', FRAME_PATH . 'src' . DS);

defined('APP_DEBUG') || define('APP_DEBUG', false);

// 环境常量
define('IS_CGI', strpos(PHP_SAPI, 'cgi') !== false ? 1 : 0);
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);
define('IS_MAC', strstr(PHP_OS, 'Darwin') ? 1 : 0);
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
define('NOW_TIME', $_SERVER['REQUEST_TIME']);

require LIBRARY_PATH . 'Core' . DS . 'Engine.php';
