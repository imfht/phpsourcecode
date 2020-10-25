<?php
namespace app\common\validate;

use think\Validate;

class Bbs extends Validate
{
    protected $rule = [
        'id|ID' => 'require|number',
        'title|标题' => 'require',
        'sorts|排序' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
        'mod'   => 'number',
        'abc|ABC'   => 'integer|confirm:id'
    ];

    protected $scene = [
        'add'   => ['title', 'sorts', 'status','id'],
        'edit'  => ['title', 'sorts', 'status','id'],
        'status' => ['status'],
        'title' => ['title','id'],
        'url' => ['url'],
        'abc' => ['abc'],
    ];

}