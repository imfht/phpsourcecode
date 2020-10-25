<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Application/');
//定义系统根目录
define('__ROOT__','/damafun');
//调试函数
function p($msg){
	echo '<pre>';
	var_dump($msg);
	echo '</pre>';
}
//定义公用资源所在目录
define('APP_RES', __ROOT__.'/Public');
//定义项目在本地文件中的地址（ffmpeg转码需要用）
define("ROOTPRO_PATH","C:/Lamp/apache24/htdocs/damafun");

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单