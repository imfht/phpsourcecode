<?php
namespace app\common\validate;

use think\Validate;

class TokenValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'name' => 'token',
    ];

}