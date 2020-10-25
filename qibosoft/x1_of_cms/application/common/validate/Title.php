<?php
namespace app\common\validate;

use think\Validate;


Title Category extends Validate
{
    //定义验证规则
    protected $rule = [
        'title|标题名称'   => 'require',
    ];

}
