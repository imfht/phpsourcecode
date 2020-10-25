<?php
namespace app\common\validate;

use think\Validate;


class Sort extends Validate
{
    //定义验证规则
    protected $rule = [
        'name|栏目名称'   => 'require',
        'mid|所属模型'  => 'require',
    ];
    
    // 定义验证提示
    protected $message = [
            'mid'        => '必须要选择模型',
    ];

}
