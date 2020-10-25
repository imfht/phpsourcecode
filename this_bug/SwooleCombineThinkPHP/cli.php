<?php
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', True);

// 定义应用目录
define('APP_PATH', dirname(__FILE__) . '/Application/');

// 定义应用模式
define('APP_MODE', 'cli');

//加载composer需要的扩展
require './vendor/autoload.php';

// 引入ThinkPHP入口文件
require dirname(__FILE__) . '/ThinkPHP/ThinkPHP.php';


