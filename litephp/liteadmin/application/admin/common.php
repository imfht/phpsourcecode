<?php
/**
 * 密码加密
 * @param $password
 * @return bool|string
 */
function auth_pwd_encrypt($password){
    $option = [
        'cost'=>config('password.cost')
    ];
    return password_hash($password,PASSWORD_DEFAULT, $option);
}

/**
 * 权限校验函数
 * @param $path
 * @return bool|\think\response|\think\response\Redirect
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function auth($path){
    return \LiteAdmin\Auth::auth($path);
}

/**
 * 创建一个错误响应 供权限检查中间件使用
 * @param $code
 * @param $message
 * @return \think\response
 */
function error_response($code,$message){

    $response = request()->isAjax()?
        json([
            'code' => 0,
            'msg'  => $message,
        ],$code):
        view('/error',['message'=>$message],$code);

    throw new \think\exception\HttpResponseException($response);
}