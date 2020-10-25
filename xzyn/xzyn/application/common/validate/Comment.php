<?php
namespace app\common\validate;

use think\Validate;

class Comment extends Validate
{
    protected $rule = [
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        //'add'   => ['status'],
        'edit'  => ['status'],
        'status' => ['status'],
    ];
}