<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'username' => 'require|unique:admin',
        'password' => 'min:6|max:16',
        'group_id' => 'require',
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'username.unique'  => '用户名已存在',
        'password.min'     => '密码长度不能小于6位',
        'password.max'     => '密码长度不能大于16位',
        'group_id'         => '请选择用户组',
    ];
}
