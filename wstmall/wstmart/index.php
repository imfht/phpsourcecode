<?php
namespace think;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.6.0','<'))  die('require PHP >= 5.6.0 !');
//进入安装目录
if(is_dir("install") && !file_exists("install/install.ok")){
	header("Location:install/index.php");
	exit();
}
// 定义应用目录
define('APP_PATH', __DIR__ . '/wstmart/');
define('DS',DIRECTORY_SEPARATOR);
// 加载基础文件
require __DIR__ . '/thinkphp/base.php';
// 执行应用并响应
Container::get('app')->path(APP_PATH)->run()->send();
