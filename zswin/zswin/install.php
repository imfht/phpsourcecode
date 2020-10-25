<?php
// +----------------------------------------------------------------------
// | MTCEO [MAKE TOGETHER CEO ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2020 http://mtceo.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: dongzi <kssoulmate@foxmail.com>
// +----------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', false);

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Install/' );

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/Install/' );

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';