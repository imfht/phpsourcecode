<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Layout extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'type_name'  =>  'require|unique:layout',
        'price'  =>  'require|number',
        'deposit'  =>  'require|number',

    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'type_name.require' => '房型不能为空',
        'type_name.unique' => '房型已存在',
        'price.require' => '价格不能为空',
        'price.number' => '价格必须是数字',
        'deposit.require' => '押金不能为空',
        'deposit.number' => '押金必须是数字',
    ];
}
