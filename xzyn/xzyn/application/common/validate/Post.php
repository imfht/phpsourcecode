<?php
namespace app\common\validate;
//贴吧帖子验证器
use think\Validate;

class Post extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'title|标题' => 'require',
        'details|内容' => 'require',
        'click|浏览数量' => 'number',
        'reply_num|回复数量' => 'number',
        'zan_num|赞数量' => 'number',
        'uid|用户UID' => 'require|number',
        'audit|状态' => 'in:0,1',
        'is_fine|精华' => 'in:0,1',
        'is_top|置顶' => 'in:0,1',
        'is_tui|推荐' => 'in:0,1',
        'orderby|排序' => 'number',
    ];

    protected $scene = [
        'add'   => ['title', 'details', 'uid', 'audit'],
//      'edit'  => ['typeid', 'title', 'click', 'status', 'create_time'],
        'audit' => ['audit','id'],
        'zan_num' => ['zan_num','id'],
    ];
}