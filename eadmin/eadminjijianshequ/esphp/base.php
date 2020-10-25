<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------


/** 定义常量 */
define('INIT_MEMORY', memory_get_usage()); //初始内存占用
define('INIT_TIME', microtime(true)); //初始运行时间
define('ESPHP_VERSION', '1.0.0'); //ESPHP 版本
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r')); //基本输出
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w')); //基本输出
defined('STDERR') or define('STDERR', fopen('php://stderr', 'w')); //基本错误

//define('DS', DIRECTORY_SEPARATOR);
define('DS', '/');
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
defined('ROOT_PATH') or define('ROOT_PATH', dirname(realpath(APP_PATH)) . DS);
defined('EXTEND_PATH') or define('EXTEND_PATH', ROOT_PATH . 'extend' . DS);
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);
defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);
defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'log' . DS);
defined('CACHE_PATH') or define('CACHE_PATH', RUNTIME_PATH . 'cache' . DS);
defined('TEMP_PATH') or define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);
defined('CONF_PATH') or define('CONF_PATH', APP_PATH); // 配置文件目录

/** 以下常量不区分大小写 */
$file = dirname(realpath(__DIR__));
define('__ROOT__', str_replace('\\', '/', $file . '/'), true); //网站根目录

define('__CORE__', __ROOT__ . 'esphp/', true); //内核目录
defined('ES_FUN') or define('ES_FUN', __CORE__ . 'functions/');
defined('ES_CLASS') or define('ES_CLASS', __CORE__ . 'classes/');

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);


error_reporting(E_ALL & ~E_STRICT); //抑制严格性错误


include ES_CLASS . 'Loader.php';

\esclass\Loader::register();
debug('app_begin');

// 注册错误和异常处理机制
\esclass\EsException::register();


set_content_type('text/html', 'UTF-8'); //设置默认文档类型
ini_set('default_charset', 'UTF-8'); //设置默认脚本编码
date_default_timezone_set(config('config.default_timezone')); //设置默认时区


\esclass\Loader::run();
debug('app_end');

write_exe_log('app_begin', 'app_end');
