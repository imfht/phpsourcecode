<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//\think\Route::rule('bbs/show/:id','\\app\\bbs\\index\\Content@show');
use think\Route;

if (is_file(RUNTIME_PATH.'routemy.php')) {      //后台URL美化插件自定义的路由规则
    include RUNTIME_PATH.'routemy.php';
}

if (is_file(APP_PATH.'routemy.php')) {      //用户自定义的路由规则
    include APP_PATH.'routemy.php';
}

Route::group(['name'=>'cms','ext'=>'html'], [
        'show-<id>'	=>['cms/content/show',['method'=>'get'],['id' => '\d+']],
        'list-<fid>'=>['cms/content/index',['method'=>'get'],['fid' => '\d+']],
        'mid-<mid>'=>['cms/content/index',['method'=>'get'],['mid' => '\d+']],
        'show'	=> 'cms/content/show',
        'list'	=> 'cms/content/index',
        'index$'	=> 'cms/index/index',
]);

// Route::group(['name'=>'shop','ext'=>'html'], [
//         'show-<id>'	=>['shop/content/show',['method'=>'get'],['id' => '\d+']],
//         'list-<fid>'=>['shop/content/index',['method'=>'get'],['fid' => '\d+']],
//         'mid-<mid>'=>['shop/content/index',['method'=>'get'],['mid' => '\d+']],
//         'show'	=> 'shop/content/show',
//         'list'	=> 'shop/content/index',
//         'index$'	=> 'shop/index/index',
// ]);

Route::group(['name'=>'bbs','ext'=>'html'], [
        'show-<id>'	=>['bbs/content/show',['method'=>'get'],['id' => '\d+']],
        'list-<fid>'=>['bbs/content/index',['method'=>'get'],['fid' => '\d+']],
        'mid-<mid>'=>['bbs/content/index',['method'=>'get'],['mid' => '\d+']],
        'show'	=> 'bbs/content/show',
        'list'	=> 'bbs/content/index',
        'index$'	=> 'bbs/index/index',
]);

Route::group(['name'=>'qun','ext'=>'html'], [
        'show-<id>'	=>['qun/content/show',['method'=>'get'],['id' => '\d+']],
        'list-<fid>'=>['qun/content/index',['method'=>'get'],['fid' => '\d+']],
        'mid-<mid>'=>['qun/content/index',['method'=>'get'],['mid' => '\d+']],
        'show'	=> 'qun/content/show',
        'list'	=> 'qun/content/index',
        'index$'	=> 'qun/index/index',
]);

// Route::group(['name'=>'hy','ext'=>'html'], [
//         'show-<id>'	=>['hy/content/show',['method'=>'get'],['id' => '\d+']],
//         'list-<fid>'=>['hy/content/index',['method'=>'get'],['fid' => '\d+']],
//         'mid-<mid>'=>['hy/content/index',['method'=>'get'],['mid' => '\d+']],
//         'show'	=> 'hy/content/show',
//         'list'	=> 'hy/content/index',
//         'index$'	=> 'hy/index/index',
// ]);

// Route::group(['name'=>'fenlei','ext'=>'html'], [
//         'show-<id>'	=>['fenlei/content/show',['method'=>'get'],['id' => '\d+']],
//         'list-<fid>'=>['fenlei/content/index',['method'=>'get'],['fid' => '\d+']],
//         'mid-<mid>'=>['fenlei/content/index',['method'=>'get'],['mid' => '\d+']],
//         'show'	=> 'fenlei/content/show',
//         'list'	=> 'fenlei/content/index',
//         'index$'	=> 'fenlei/index/index',
// ]);

Route::group(['name'=>'p','ext'=>'html'], [
        '<plugin_name>-<plugin_controller>-<plugin_action>'	=>['index/plugin/execute',['method'=>'get|post'],['plugin_name' => '[a-z_0-9]+','plugin_controller' => '[a-z_0-9\.]+','plugin_action' => '[a-z_0-9]+',]],
]);

Route::group(['name'=>'page','ext'=>'html'], [
        '<id>$'	=>['index/alonepage/index',['method'=>'get'],['id' => '\d+']],
]);

Route::group(['name'=>'home','ext'=>'html'], [
        '<uid>$'	=>['member/user/index',['method'=>'get'],['uid' => '\d+']],
]);

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];
