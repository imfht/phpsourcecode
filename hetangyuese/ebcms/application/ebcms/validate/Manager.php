<?php
namespace app\ebcms\validate;

use think\Validate;

class Manager extends Validate
{

    protected $rule = [
        'email|邮箱' => 'require|email|unique:manager',
        'nickname|昵称' => 'require|max:10|unique:manager',
        'password|密码' => 'require|min:6|max:12',
    ];

    protected $scene = [
        'add' => ['email', 'nickname','password'],
        'edit' => ['email', 'nickname'],
        'password' => ['password'],
    ];
}