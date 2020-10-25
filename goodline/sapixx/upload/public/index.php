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
namespace think;
// 检测是否是新安装
if(file_exists("./install") && !file_exists("./install/install.lock")){
    // 组装安装url
    $url=$_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'],'index.php').'install/index.php';
    // 使用http://域名方式访问；避免./public/install 路径方式的兼容性和其他出错问题
    header("Location:http://$url");
    die;
}
require __DIR__ . '/../framework/base.php';
// 执行应用并响应
Container::get('app')->run()->send();