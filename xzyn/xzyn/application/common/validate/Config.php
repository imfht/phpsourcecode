<?php
namespace app\common\validate;

use think\Validate;

class Config extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'k|键' => 'require',
        'v|值' => 'require',
        'type|类型' => 'require',
        'texttype|文本类型' => 'require',
        'sorts|排序' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['k', 'v', 'type', 'texttype', 'sorts', 'status'],
        'edit'  => ['k', 'v', 'type', 'texttype', 'sorts', 'status','id'],
        'status' => ['status','id'],
        'k' => ['k','id'],
        'v' => ['v','id'],
        'desc' => ['desc','id'],
        'type' => ['type','id'],
    ];
}