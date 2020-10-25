<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


Route::alias('/', 'Index/index');##首页别名
Route::alias('sitemap', 'home/Index/sitemap');##网站地图别名


//如果有引导页 ,并注释 19行 / 的别名
//Route::alias('', 'home/Index/guide');##默认打开
//Route::alias('index', 'home/Index/index');##首页别名

Route::alias('login', 'manage/User/login');
Route::alias('register', 'manage/User/register');
Route::alias('plugin', 'plugin/Plugin/index');


//****收集URL别名，以避免被绑定到默认模块而无法找到指定模块*****
$alias = ['login', 'plugin', 'sitemap', 'register'];

$pathinfo = isset($_SERVER['PATH_INFO']) ? ((substr($_SERVER['PATH_INFO'],0,1) != '/') ? $_SERVER['PATH_INFO'] :substr($_SERVER['PATH_INFO'],1)) :'';
$pathinfo = explode('/', strpos($pathinfo, '.html') === false ? $pathinfo : substr($pathinfo,0,-5));
//允许可以省略默认模块名  /User/login  =>/home/User/login
if(!in_array(strtolower($pathinfo[0]), config('allow_module_list')) &&  strtolower($pathinfo[0]) != config('default_module') && !in_array(strtolower($pathinfo[0]), $alias)) {
    Route::bind(config('default_module'));
}

return [

];
