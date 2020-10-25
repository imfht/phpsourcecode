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
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

require_once "../../../class/connect.php";//引用后台管理工具包
require_once '../../../config/config.php';
define("ROOTPATH", ECMS_PATH) ;
// 绑定到 api 模块
define('BIND_MODULE','view');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
