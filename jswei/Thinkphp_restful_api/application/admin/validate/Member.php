<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\admin\validate;
use think\Validate;

class Member extends Validate
{
    protected $rule = [
        'username'  => 'require',
        'password' => 'require'
    ];

    protected $message  =   [
        'username.require'      => '用户名必须',
        'password.require'      => '密码必须'
    ];
}