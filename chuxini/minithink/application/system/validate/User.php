<?php
/*
* 
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/2
*/
namespace app\system\validate;

use think\Validate;

class User extends Validate{
    protected $rule = [
        'username'  =>  'require|min:5|max:20|unique:user'
    ];

    protected $message  =   [
        'username.require' => '用户名必须存在',
        'username.min'     => '用户名最少不能低于5个字符',
        'username.max'     => '用户名最多不能超过20个字符',
        'username.unique'  => '用户名已存在',
    ];
}