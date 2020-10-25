<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Building extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'building'  =>  'require|unique:building',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'building.require' => '楼栋不能为空',
        'building.unique' => '楼栋已存在',
    ];
}
