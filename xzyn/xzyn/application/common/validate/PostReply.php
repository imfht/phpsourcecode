<?php
namespace app\common\validate;
//贴吧回复验证器
use think\Validate;

class PostReply extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'contents|内容' => 'require',
        'post_id|帖子ID' => 'number',
        'zan_num|赞数量' => 'number',
        'uid|用户UID' => 'require|number',
        'audit|状态' => 'in:0,1',
    ];

    protected $scene = [
        'add'   => ['contents', 'uid', 'audit'],
//      'edit'  => ['title', 'title', 'click', 'status', 'create_time'],
        'audit' => ['audit','id'],
        'zan_num' => ['zan_num','id'],
    ];
}