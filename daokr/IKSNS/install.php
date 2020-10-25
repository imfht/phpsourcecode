<?php
// +----------------------------------------------------------------------
// | IKPHP.COM [ I can do all the things that you can imagine ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2050 http://www.ikphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小麦 <810578553@qq.com> <http://www.ikcms.cn>
// +----------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('要求 PHP > 5.3.0 !');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', true );
define ( 'BIND_MODULE','Install');
/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Apps/' );
define ( 'IKPHP_DATA', './Data/' );
/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );

if (is_file(IKPHP_DATA.'install.lock')) {
	header('Location: ./index.php');
	exit;
}
//载入版本号 删除后将无法升级系统
$arr_ikversion = require_once('version.php');
//定义全局版本序列
foreach ($arr_ikversion as $key => $val){
	define($key, $val);
}

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';