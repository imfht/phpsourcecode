<?php
namespace app\common\validate;
//模版验证器
use think\Validate;

class Template extends Validate
{
    protected $rule = [
        'puth_name|目录名称' => 'require|alphaDash',
        'name|模版名称' => 'require',
        'status|状态' => 'in:0,1',
    ];

    protected $scene = [
        // 'add'   => ['app_name','app_limitTime'],
    ];
}