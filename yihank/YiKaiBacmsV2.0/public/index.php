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
if (!defined('__ROOT__')) {
    $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
    define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
}
if(file_exists("install") && !file_exists("install.lock")){
    // 组装安装url
    $url = $_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'],'index.php').'install/index.php';
    // 使用http://域名方式访问；避免./public/install 路径方式的兼容性和其他出错问题
    header("Location:http://$url");die;
}
//模板路径
define('THEME_NAME', 'template');
// [ 应用入口文件 ]
//常量定义
define('NOW_TIME',date('Y-m-d H:i'));
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('ROOT_PATH', __DIR__ . '/../');
define('SITE_URL','http://'.$_SERVER['HTTP_HOST']); //
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
