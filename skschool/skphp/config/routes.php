<?php
/**
 // +-------------------------------------------------------------------
 // | SKPHP [ 为web梦想家创造的PHP框架。 ]
 // +-------------------------------------------------------------------
 // | Copyright (c) 2012-2016 http://sk-school.com All rights reserved.
 // +-------------------------------------------------------------------
 // | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 // +-------------------------------------------------------------------
 // | Author:
 // | seven <seven@sk-school.com>
 // | learv <learv@foxmail.com>
 // | ppogg <aweiyunbina3@163.com>
 // +-------------------------------------------------------------------
 // | Knowledge change destiny, share knowledge change you and me.
 // +-------------------------------------------------------------------
 // | To be successful
 // | must first learn To face the loneliness,who can understand.
 // +-----------------------------------------------------------------*/
use Skschool\Routing as Route;


// 自定义定义正则
Route::$patterns = [
	':num' => '[0-9]+',
	':all' => '.*',
	':id' => '.*',
	':Nid' => '.*',
	':p' => '.*',
];


// Demo1 不经过控制器方法直接加载视图（实战应用：关于我们，帮助中心等单页）
Route::get('/', function() {
	return view('index');
});

// Demo2 Index控制器，index方法（无分组） 
Route::get('/demo2', 'IndexController@index');

// Demo3 Index控制器，index方法（admin分组）
Route::get('/demo3', 'admin\IndexController@index');

// cache
Route::get('/cache', 'IndexController@cache');

// config
Route::get('/config', 'IndexController@config');

// Page
Route::get('/page-(:p)','PageController@index');

// Yzm
Route::get('/yzm','YzmController@index');
Route::get('/yzmShow','YzmController@yzm');


// other demo
Route::get('/article2-(:Nid)-(:num)-(:id)','admin\IndexController@index');

Route::get('/article-(:num)-(:Nid)', function($a,$b) {
	echo "文章 number".$a.$b;
});

// DB 
Route::get('/db1','DbController@skphp');
Route::get('/db2','DbController@laravel');


Route::get('(:all)', function($fu) {
	// echo '未匹配到路由<br>'.$fu;
	return view('404');
});
