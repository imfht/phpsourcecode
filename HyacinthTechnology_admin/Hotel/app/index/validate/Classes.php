<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Classes extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'classe'  =>  'require|unique:classes',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'classe.require' => '班次不能为空',
        'classe.unique' => '班次已存在'
    ];
}
