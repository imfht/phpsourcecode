<?php
namespace app\common\validate;

use think\Validate;

class Guestbook extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'title|标题' => 'require',
        'email|邮箱' => 'email',
        'status|状态' => 'require|in:0,1',
        'content|内容' => 'require',
    ];

    protected $scene = [
        'add'   => ['title', 'email', 'status', 'content'],
        'edit'  => ['title', 'email', 'status', 'content','id'],
        'status' => ['status','id'],
    ];
}