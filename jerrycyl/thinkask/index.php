<?php


// [ *********应用入口文件 此入口只建议虚拟机这样配置，为了更加的安全，请配置目录为 public 入口文件public/index ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('PUBLIC_PATH', __DIR__ . '/public/');
define('PLUS_PATH', __DIR__ .'/plus/');
define('THINKASK_VERSION', 'v1.2.3');
define('__STATIC__', '/public');

// 加载框架引导文件
require './core/start.php';

// TP文档：http://www.kancloud.cn/manual/thinkphp5/129746
