<?php
namespace app\common\validate;

use think\Validate;


class Module extends Validate
{
    //定义验证规则
    protected $rule = [
        'title|模型名称'   => 'require',
    ];

}
