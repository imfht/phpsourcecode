<?php
namespace app\common\validate;
//模型验证器
use think\Validate;

class ArctypeMod extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'name|模型名称' => 'require',
        'mod|文章模型' => 'require|alpha',
        'sorts|排序' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['name', 'mod', 'sorts', 'status'],
        'edit'  => ['name', 'mod', 'sorts', 'status','id'],
        'status' => ['status','id'],
    ];
}