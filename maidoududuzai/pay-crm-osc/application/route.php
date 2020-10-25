<?php

use \think\Route;

//Alias
//Route::alias('home', 'home/index/index');

Route::rule('t/:str', 'home/url/index');
Route::rule('p/:qid', 'pay/index/index');

Route::rule('wx_auth', 'wechat/WxOpen/auth');
Route::rule('wx_push', 'wechat/WxOpen/push');


Route::rule('wx/:merchant_no', 'wechat/index/index');

Route::rule('mock/:module/:controller/:action', 'mock/index/index?r=:module/:controller/:action');


return [

];

