<?php
namespace app\api\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'phone'  => 'unique:user',
    ];

    protected $message  =   [
        'phone.unique' => '手机号已存在',
    ];

}