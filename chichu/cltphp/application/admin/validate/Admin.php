<?php
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule =   [
        'username'  => 'require|length:3,25',
        'email'     =>'email'
    ];
    protected $message  =   [
        'username.require'      => '用户名不能为空',
        'username.length'       => '用户名在3到25个字符之间',
        'email.email'           => '邮箱格式不正确',
    ];
}