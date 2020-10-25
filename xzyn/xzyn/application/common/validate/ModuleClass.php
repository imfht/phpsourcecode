<?php
namespace app\common\validate;

use think\Validate;

class ModuleClass extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'title|标题' => 'require',
        'action|模块' => 'require|alpha',
        'sorts|排序' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['title', 'action', 'sorts', 'status'],
        'edit'  => ['title', 'sorts', 'status','id'],
        'status' => ['status','id'],
        'title' => ['title','id'],
    ];
}