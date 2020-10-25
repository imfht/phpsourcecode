<?php
namespace app\common\validate;

use think\Validate;


Name Category extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|标题名称'   => 'require',
    ];

}
