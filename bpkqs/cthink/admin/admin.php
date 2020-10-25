<?php
//默认插件
define('BIND_MODULE','admin');

// 定义应用目录
define('APP_PATH','apps/');

//重新定义extend的路径
define('EXTEND_PATH',__DIR__.'/extend/');

//引入composer的autoload
require __DIR__ . '/../system/vendor/autoload.php';

// 加载框架引导文件
require __DIR__ . '/../system/thinkphp/start.php';
