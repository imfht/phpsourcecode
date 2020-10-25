<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Storey extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'storey'  =>  'require|unique:storey',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'storey.require' => '楼层不能为空',
        'storey.unique' => '楼层已存在'
    ];
}
