<?php
namespace app\common\validate;

use think\Validate;

class AuthGroup extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'title|角色名称' => 'require',
        'level|角色等级' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
        'module|所属模块' => 'require',
    ];

    protected $scene = [
        'add'   => ['title', 'level', 'status', 'module'],
        'edit'  => ['title', 'level', 'status', 'module','id'],
        'status' => ['status','id'],
        'title' => ['title','id'],
        'level' => ['level','id'],
        'notation' => ['notation','id'],
    ];
}