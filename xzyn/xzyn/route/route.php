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

Route::rule('detail/:dirs/:id', 'index/Detail/index', 'GET');   //文章
Route::rule('category/:dirs', 'index/Category/index', 'GET');   //栏目
Route::rule('userinfo/:uid', 'index/userinfo/index', 'GET',[],['uid'=>'[0-9]+']);   //个人主页
Route::get('verify','index/login/verify');	//验证码
