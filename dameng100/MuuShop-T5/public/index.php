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

//模块目录
define('APP_PATH', __DIR__ . '/../application/');
//插件目录
define('ADDONS_PATH', __DIR__ . '/../addons/');
//类库目录
define('EXTEND_PATH', __DIR__ . '/../extend/');
//public目录
define('PUBLIC_PATH', __DIR__);
// 判断是否安装MuuCmf
if (!is_file(__DIR__ . '/../install.lock'))
{	
    header("location:./install.php");
    exit;
}

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
