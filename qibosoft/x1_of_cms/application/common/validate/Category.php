<?php
namespace app\common\validate;

use think\Validate;


class Category extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|栏目名称'   => 'require',
    ];

}
