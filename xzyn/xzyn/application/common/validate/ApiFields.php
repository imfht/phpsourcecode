<?php
namespace app\common\validate;
//接口验证器
use think\Validate;

class ApiFields extends Validate
{
    protected $rule = [
        'fieldName|字段名称' => 'require|alphaDash',
        'dataType|数据类型'  => 'require|in:1,2,3,4,5,6,7,8,9,10,11,12,13',
    ];

    protected $scene = [
        'add'   => ['fieldName','dataType'],
        'edit'  => ['fieldName','dataType'],
        'fieldName' => ['fieldName'],
        'default' => ['default'],
        'info' => ['info'],
    ];
}