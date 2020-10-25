<?php
namespace app\common\validate;

use think\Validate;

class Flink extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'webname|网站名称' => 'require',
        'email|站长email' => 'email',
        'sorts|排序' => 'require|integer|>=:1',
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['webname', 'email', 'sorts', 'status'],
        'edit'  => ['webname', 'email', 'sorts', 'status','id'],
        'status' => ['status','id'],
        'webname' => ['webname','id'],
        'url' => ['url','id'],
    ];
}