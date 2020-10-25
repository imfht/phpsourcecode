<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Payment extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'pay_name'  =>  'require|unique:payment',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'pay_name.require' => '支付方式不能为空',
        'pay_name.unique' => '支付方式已存在'
    ];
}
