<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Identity extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'identity'  =>  'require|unique:identity',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'identity.require' => '证件类型不能为空',
        'identity.unique' => '证件类型已存在'
    ];
}
