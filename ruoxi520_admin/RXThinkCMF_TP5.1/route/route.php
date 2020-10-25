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

/**
 * 前台index模块路由配置
 */
Route::domain('www', 'index');

/**
 * 后台admin模块路由配置
 */
Route::domain('admin', 'admin');

/**
 * 接口API模块路由配置
 */
Route::domain('api', 'api');

/**
 * 脚本script模块路由配置
 */
Route::domain('script', 'script');

/**
 * WAP站wap模块路由配置
 */
Route::domain('m', 'wap');

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::get('hello/:name', 'index/hello');

return [

];
