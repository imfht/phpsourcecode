<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Purchases extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'number'  =>  'require|number',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'number.require' => '数量不能为空',
        'number.number' => '数量必须是数字',
    ];
}
