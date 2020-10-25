<?php

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('Require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 绑定访问Admin模块
define('BIND_MODULE','Publish');
define('BIND_CONTROLLER','Index');
define('BIND_ACTION','upload');

// 定义应用目录
define('APP_PATH','./Application/');

// 引入ThinkPHP入口文件
require_once('./Engine/index.php');

// 亲^_^ 后面不需要任何代码了 就是如此简单