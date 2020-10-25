<?php

/*
|--------------------------------------------------------------------------
| Main Routes
|--------------------------------------------------------------------------
*/

/* 默认 入口*/
Route::any('/index/index', "IndexController@index");
/* index - enum */
Route::any('/index/enum', "IndexController@enum");
/* index - upImage 上传图片 */
Route::any('/index/upFile', "IndexController@upFile");
/* index - qrCode 二维码 */
Route::any('/index/qrCode', "IndexController@qrCode");

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

/* auth - getMemberList */
Route::any('auth/loginByEmail', "AuthController@loginByEmail");
/* auth - logout */
Route::any('auth/logout', "AuthController@logout");
/* auth - logout */
Route::any('auth/getRegCode', "AuthController@getRegCode");
/* auth - logout */
Route::any('auth/register', "AuthController@register");
/* auth - logout */
Route::any('auth/resetPwdByCode', "AuthController@resetPwdByCode");
/* auth - loginByWx */
Route::any('auth/loginByWx', "AuthController@loginByWx");
/* auth - loginByWeb */
Route::any('auth/loginByWeb', "AuthController@loginByWeb");
/* auth - loginByMini */
Route::any('auth/loginByMini', "AuthController@loginByMini");
/* auth - getCaptcha */
Route::any('auth/getCaptcha', "AuthController@getCaptcha");
/* auth - getCaptcha */
Route::any('auth/getWxLoginQr', "AuthController@getWxLoginQr");
/* auth - getCaptcha */
Route::any('auth/checkWxLoginQr', "AuthController@checkWxLoginQr");

/*
|--------------------------------------------------------------------------
| Self
|--------------------------------------------------------------------------
*/

/* self - resetPwdByOld */
Route::any('self/resetPwdByOld', "SelfController@resetPwdByOld");
/* self - info */
Route::any('self/info', "SelfController@info");
/* self - loginByQrToken */
Route::any('self/loginByQrToken', "SelfController@loginByQrToken");


/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

/* user - getMemberList */
Route::any('user/listsMember', "UserController@listsMember");
/* user - editMember */
Route::any('user/editMember', "UserController@editMember");
/* user - deleteMember */
Route::any('user/deleteMember', "UserController@deleteMember");
/* user - getGroupList */
Route::any('user/listsRole', "UserController@listsRole");
/* user - editGroup */
Route::any('user/editRole', "UserController@editRole");
/* user - deleteGroup */
Route::any('user/deleteRole', "UserController@deleteRole");
/* user - getAuthList */
Route::any('user/listsPermission', "UserController@listsPermission");
