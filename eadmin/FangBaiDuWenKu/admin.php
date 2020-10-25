<?php
// 检测程序安装

if(!is_file(__DIR__ . '/install/install.lock')){
	header('Location: ./install/index.php');
	exit;
}


// 定义应用目录
define('APP_PATH', __DIR__ . '/app/');
define('BIND_MODULE','admin');

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';