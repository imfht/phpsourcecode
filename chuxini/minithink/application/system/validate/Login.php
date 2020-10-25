<?php
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/3
*/
namespace app\system\validate;

use app\system\model\Auth;
use app\system\model\User;
use think\Validate;
class Login extends Validate{
    protected $rule = [
        'user'  =>  'require|min:5|max:20|checkState:1',
        'pwd'  =>  'require',
        'captcha'   =>  'require|captcha'
    ];

    protected $message  =   [
        'user.require' => '用户名必须存在',
        'user.min'     => '用户名最少不能低于5个字符',
        'user.max'     => '用户名最多不能超过20个字符',
        'pwd.require'  => '密码必须存在',
        'captcha.require'  => '验证码必须存在',
        'captcha.captcha'  => '验证码错误',
    ];

    // 检查用户或管理角色是否被禁用
    protected function checkState($value,$rule)
    {
        $userinfo = User::get(['username'=>$value]);
        if($userinfo->state != $rule){
            return '用户 '.$value.' 已被禁用，请联系管理员启用';
        }

        $authinfo = Auth::get(['id'=>$userinfo->getData('role')]);
        if($authinfo->state != $rule){
            return '角色 '.$authinfo->name.' 已被禁用，请联系管理员启用';
        }

        return true;
    }
}