<?php
// +----------------------------------------------------------------------
// | IKPHP.COM [ I can do all the things that you can imagine ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2050 http://www.ikphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小麦 <810578553@qq.com> <http://www.ikphp.cn>
// +----------------------------------------------------------------------

// 爱客开源多应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('要求 PHP > 5.3.0 !');
define ( 'IN_IK', true );
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

//网站根路径设置
define('SITE_PATH', dirname(__FILE__));
// 定义应用目录
define('APP_PATH','./Apps/');
define ('IKPHP_DATA', './Data/' );
// 定义插件路径
define('IKPHP_ADDON_PATH', './Addons/');

if (!is_file(IKPHP_DATA.'install.lock')) {
	header('Location: ./install.php');
	exit;
}
//载入版本号 删除后将无法升级系统
$arr_ikversion = require_once('version.php');
//定义全局版本序列
foreach ($arr_ikversion as $key => $val){
	define($key, $val);
}
/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单