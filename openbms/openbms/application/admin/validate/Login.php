<?php

namespace app\admin\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'captcha'  => 'require|captcha',
    ];

    protected $message = [
        'username.require' => '请输入账号',
        'password.require' => '请输入密码',
        'captcha.require'  => '请输入验证码',
        'captcha.captcha'  => '验证码错误',
    ];
}
