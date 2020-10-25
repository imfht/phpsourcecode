<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Guest extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'guest'  =>  'require|unique:guest',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'guest.require' => '宾客来源不能为空',
        'guest.unique' => '宾客来源已存在'
    ];
}
