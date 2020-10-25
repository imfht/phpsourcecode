<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


$api = app('Dingo\Api\Routing\Router');


$api->version('v1',['middleware' => 'fish'],function($api){
    /**
     * 获取数据
     */
    $api->post('/get-data','App\Http\Controllers\DataSyncController@getData');

    /**
     * 更新数据
     */
    $api->post('/set-data','App\Http\Controllers\DataSyncController@setData');
});

$api->version('v1',function($api){
    /**
     * 图片上传的接口
     */
    $api->get('/upload-image','App\Http\Controllers\DataSyncController@uploadImage');
    $api->post('/upload-image','App\Http\Controllers\DataSyncController@uploadImage');
    $api->get('/image','App\Http\Controllers\DataSyncController@image');
});


/**
 * 用户登录和token相关
 */
$api->version('v1',['middleware' => 'api.throttle', 'limit' => 50,'expires' => 1],function($api){
    /**
     * 刷新用户的token
     * in:
     * [
     * 'token'=>'required|string',
     * ]
     *
     * out:
     * [
     * 'error' => 0,
     * 'token' => $token,
     * ]
     */
    $api->post('/refresh','App\Http\Controllers\AuthenticateController@refresh');


    /**
     * 用户登录
     * in:
     * [
     * 'stuId'=>'required|string',
     * 'password'=>'required|string',
     * ]
     *
     * out:
     * [
     * 'error' => 0,
     * 'token' => $token,
     * ]
     */
    $api->post('/sign-in','App\Http\Controllers\AuthenticateController@signIn');

    /**
     * 用户退出
     * in:
     * [
     * 'token'=>'required|string'
     * ]
     *
     * out:
     * [
     * 'error' => 0,
     * ]
     */
    $api->post('/sign-out','App\Http\Controllers\AuthenticateController@signOut');

    /**
     * 用户注册
     * in:
     * [
     *  'name'=>'required|string',
     *  'email'=>'required|string',
     *  'password'=>'required|string',
     *  'code'=>'required|string',
     * ]
     *
     * out:
     * [
     * 'error' => 0
     * ]
     */
    $api->post('/sign-up','App\Http\Controllers\AuthenticateController@signUp');
});

/**
 * app接口，测试接口
 */
$api->version('v1', [],function($api){
	$api->get('/dingo',function(){
        return [
            'error' => 0,
            'userInfo' => '12',
            'token' => 'sdaflk',
        ];
	});
    $api->post('/dingo',function(){
        return 'hello dingo';
    });
});
