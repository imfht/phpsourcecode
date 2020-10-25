<?php
namespace app\common\validate;

use think\Validate;


class Content extends Validate
{
    //定义验证规则
    protected $rule = [
        'title|标题'   => 'require',
    ];

}
