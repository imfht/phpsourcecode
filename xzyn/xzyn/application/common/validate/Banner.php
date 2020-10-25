<?php
namespace app\common\validate;

use think\Validate;

class Banner extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'title|标题' => 'require',
        'sorts|排序' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['title', 'sorts', 'status'],
        'edit'  => ['title', 'sorts', 'status','id'],
        'status' => ['status','id'],
        'title' => ['title','id'],
        'url' => ['url','id'],
    ];
}