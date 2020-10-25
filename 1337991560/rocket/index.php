<?php

/**
 * 入口文件
 * @author 徐亚坤 hdyakun@sina.com
 */

define('START_TIME', microtime(true));
define('START_USAGE_MEMORY', memory_get_usage());
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(E_ALL);
            break;

        case 'testing':
        case 'production':
            error_reporting(0);
            break;

        default:
            exit('The application environment is not set correctly.');
    }
}

// 目录间隔符
define("DS", DIRECTORY_SEPARATOR);
// 根目录
define('BASEPATH', __DIR__ . DS);

define('APP_FOLDER', 'app');
define('VIEW_FOLDER', 'views');
define('LAYOUT_FOLDER', 'layouts');
define('CONFIG_FOLDER', 'configs');
define('LOG_FOLDER', 'logs');
define('CACHE_FOLDER', 'caches');
define('STORAGE_FOLDER', 'storages');

// 应用目录
define('APP_PATH', BASEPATH . APP_FOLDER . DS);
// 应用视图目录
define('VIEW_PATH', APP_PATH . VIEW_FOLDER . DS);
// Layout目录
define('LAYOUT_PATH', APP_PATH . LAYOUT_FOLDER . DS);
// 应用配置目录
define('CONFIG_PATH', APP_PATH . CONFIG_FOLDER . DS);
// 应用存储目录
define('STORAGE_PATH', APP_PATH . STORAGE_FOLDER . DS);
// 应用日志目录
define('LOG_PATH', STORAGE_PATH . LOG_FOLDER . DS);
// 应用缓存目录
define('CACHE_PATH', STORAGE_PATH . CACHE_FOLDER . DS);

// 加载文件函数
function import($filepath, $base = null, $key = null)
{
    static $paths;
    $keypath = $key ? $key . $filepath : $filepath;

    if (!isset($paths[$keypath]) or empty($paths[$keypath])) {
        $base = is_null($base) ? BASEPATH : rtrim($base, '/') . DS;
        $path = str_replace('.', DS, $filepath);
        $paths[$keypath] = include_once $base . $path . '.php';
    }

    return $paths[$keypath];
}

// bootstrap
import('bootstrap.autoload');
import('bootstrap.start');
// events
import('events', APP_PATH);
// CLI 请求
if (is_cli()) {
    if ($argv[1]) {
        $re = import('cli.' . $argv[1]);
        if (!$re) {
            exit("CLI 脚本不存在。\r\n");
        }
    } else {
        exit("CLI 脚本名称缺失。\r\n");
    }
} else {
    // Routes
    import('routes', APP_PATH);
}