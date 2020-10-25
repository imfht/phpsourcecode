<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
// [ 应用入口文件 ]

header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: content-type,authorization");

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('APP_VERSION', '7.5');
define('APP_VERSION_TIME', '2020.6.16');
define('DATA_PATH', __DIR__ . '/data/');

// 检查是否安装
if (!is_file('./data/install.lock')) {
	// 检测PHP环境
	if (version_compare(PHP_VERSION, '5.4.0', '<')) {
		die('require PHP > 5.4.0 !');
	}

	// 绑定模块
	define('BIND_MODULE', 'install');
}

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
