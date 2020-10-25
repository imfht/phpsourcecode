<?php

namespace app\admin\validate;

use think\Validate;

class AuthGroup extends Validate
{
    protected $rule = [
        'name' => 'require',
    ];

    protected $message = [
        'name.require' => '用户组不能为空',
    ];
}
