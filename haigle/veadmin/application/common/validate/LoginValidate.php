<?php
namespace app\common\validate;

use think\Validate;

class LoginValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'username'    => 'require',
        'password'    => 'require'
    ];

    protected $message = [
        'username.require'      => '登录名/手机/邮箱不能为空',
        'password.require'      => '密码不能为空'
    ];

}