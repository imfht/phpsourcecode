<?php

// 定义应用目录
define('APP_PATH', __DIR__ . '/../app/');
//定义模板路径(相对路径需要)
define('TPL_PATH', dirname($_SERVER['SCRIPT_NAME']));
// 绑定当前访问到install模块
define('BIND_MODULE','install');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';