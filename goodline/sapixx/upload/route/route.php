<?php
/**
 * @copyright   Copyright (c) 2018 http://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 默认路由配置
 */
use think\facade\Route;
//后台管理
Route::group('/',['/' => 'system/home.index/index','/review/:id'=>'system/home.index/review']);
//后台管理
Route::group('system/admin',[
    '/'  => 'system/admin.index/index',
    '/login'  => 'system/admin.index/login',
    '/logout' => 'system/admin.index/logout']
)->completeMatch(true);
//会员后台
Route::group('system/passport',[
    '/'            => 'system/passport.index/index',
    '/login'       => 'system/passport.login/index',
    '/cloud'       => 'system/passport.login/cloud',
    '/reg'         => 'system/passport.login/reg',
    '/getpassword' => 'system/passport.login/getPassword',
    '/logout'      => 'system/passport.login/logout']
)->completeMatch(true);
//微信开放平台授权
Route::rule('wechatopen/ticket','system/event.wechatOpen/ticket');
Route::rule('wechatopen/:appid/message','system/event.wechatOpen/message');
//微信云市场接入
Route::rule('wechatopen/tencentmarket/auth','system/event.tencentMarket/auth');
//微信公众号授权登录
Route::rule('wechatauth/:appid/oauth','system/event.wechatAccount/index');
Route::rule('wechatauth/createcodes','system/event.wechatAccount/createCodes');
Route::rule('wechatauth/qrcodes','system/event.wechatAccount/qrCodes');
Route::rule('mp/putWechat/:app','system/event.wechatMp/putWechat')->completeMatch(true);
Route::rule('mp/getWechat/:app','system/event.wechatMp/getWechat')->completeMatch(true);
//参数过滤
Route::pattern([
    'app'        => '\d+',
    'appid'      => '\w+',
    'id'         => '\d+',
    'version'    => '[0-9a-zA-Z]+',
    'module'     => '[a-zA-Z]+',
    'controller' => '[a-zA-Z]+',
    'action'     => '[a-zA-Z]+'
]);