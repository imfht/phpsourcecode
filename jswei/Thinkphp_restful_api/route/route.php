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
//
//Route::get('think', function () {
//    return 'hello,ThinkPHP5!';
//});
//
//Route::get('hello/:name', 'index/hello');
use think\facade\Route;
Route::group('v1',function (){
    Route::get('user/send_code','first/user/sendCode');
    Route::get('user/check_code','first/user/checkCode');
    Route::get('user/personal','first/user/personal');
    Route::post('user/hobbies','first/user/hobbies');
    Route::post('user/info','first/user/info');
    Route::post('user/head','first/user/head');
    Route::post('user/nickname','first/user/nickname');
    Route::post('user/sex','first/user/sex');
    Route::post('user/password','first/user/password');
    Route::post('user/phone','first/user/phone');
    Route::any('get_access_token','first/auth/accessToken');
    Route::any('access_token','first/auth/accessToken');
    Route::resource('user','first/user');
    /*-----------------------------------------------------*/
    Route::resource('navbar','first/navbar');
    /*-----------------------------------------------------*/
    Route::resource('carousel','first/carousel');
    /*-----------------------------------------------------*/
})->allowCrossDomain();

Route::group('admin',function (){
    Route::resource('column','admin/column');
    Route::resource('message','admin/message');
    Route::resource('member','admin/member');
    Route::resource('module','admin/module');
    Route::resource('group','admin/group');
    Route::resource('admin','admin/admin');
    Route::resource('carousel','admin/carousel');
    Route::resource('blogroll','admin/blogroll');
    Route::resource('setting','admin/setting');
})->allowCrossDomain();

Route::group('chat',function (){
    Route::get('members','chat/chat/getMembers');
    Route::post('uploads','chat/uploadify/uploads');
})->allowCrossDomain();

Route::post('posts','admin/Uploadify/upload')->allowCrossDomain();
Route::delete('posts','admin/Uploadify/delete')->allowCrossDomain();
Route::get('wiki','first/first/Wiki/index');
Route::any('upload/qiniu','first/Uploadify/toQiNiu');
Route::any('uploads','first/Uploadify/uploads')->allowCrossDomain();
Route::any('upload','index/index/upload');
