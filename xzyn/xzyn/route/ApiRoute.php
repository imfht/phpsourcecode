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

// API 统一接口路由

// 后置行为执行 行为
$afterBehavior = ['\app\api\behavior\ApiAuth', '\app\api\behavior\RequestFilter'];
Route::rule('api/:hash','api/base/iniApi')->after( $afterBehavior )->middleware([app\http\middleware\Common::class])
    ->header('Access-Control-Allow-Origin','*')
    ->header('Access-Control-Allow-Headers:UserToken,APPToken')
    ->header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
// API文档列表
Route::rule('apilist','api/apihelp.index/index','GET');
// API文档详情
Route::rule('apiinfo/:hash','api/apihelp.index/apiinfo','GET');
// API错误码详情
Route::rule('errorlist','api/apihelp.index/errorlist','GET');