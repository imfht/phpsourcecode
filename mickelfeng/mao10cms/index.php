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
header("Content-Type: text/html; charset=UTF-8");
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('PHP版本必须为5.3以上！');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Application/');

//若服务器支持 开启Gzip
define ( "GZIP_ENABLE", function_exists ( 'ob_gzhandler' ) );
ob_start ( GZIP_ENABLE ? 'ob_gzhandler' : null );

// 引入ThinkPHP入口文件
require './Engine/index.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单