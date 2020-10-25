<?php
/**
 * @package     autoload.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年4月5日
 */

use \SlimCustom\Libs\App;

$baseDir = dirname(dirname(__DIR__)) . '/';

// 注册自动文件自动加载方法
spl_autoload_register(function ($classname) use ($baseDir) {
    $halfFileInfo = str_replace('\\', '/', $classname) . '.php';
    $file = $baseDir . $halfFileInfo;
    // 默认先从框架加载
    if (is_file($file)) {
        return require_once $file;
    }
    // 尝试从应用加载
    elseif (is_file($file = dirname(App::$instance->path()) . '/' . $halfFileInfo)) {
        return require_once $file;
    }
    else {
        return false;
    }
});

// 加载composer autoload文件
require_once  __DIR__ . '/../vendor/autoload.php';

