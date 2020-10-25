<?php
/**
 * 「PHP联盟」
 * 入口文件定义
 * 楚羽幽 <Name_Cyu@Foxmail.com>
 */

// 判断PHP版本
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('PHP版本必须是 5.3.0 以上 ，「PHP联盟」提示!');

//设置时区
date_default_timezone_set( 'PRC' );

// 定义框架路径
define('UNION_PATH','./Union');

// 开启调试模型
define('DEBUG',True);

// 显示DEBUG面板
// define('DEBUG_TOOL',True);

// 应用目录
define('APP_PATH','Home/');

// 缓存目录
define('TEMP_PATH','./Cache/');

// 缓存文件
define('TEMP_FILE',TEMP_PATH.'~Cache.php');

// 引入框架文件
require UNION_PATH.'/Union.php';