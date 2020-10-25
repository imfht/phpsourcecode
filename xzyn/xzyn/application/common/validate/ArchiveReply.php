<?php
namespace app\common\validate;
//文章回复验证器
use think\Validate;

class ArchiveReply extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'contents|内容' => 'require',
        'aid|文章ID' => 'require',
        'uid|用户ID' => 'require|number',
        'audit|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['contents', 'aid'],
        'edit'  => ['contents', 'audit','id'],
        'audit' => ['audit','id'],
        'zan_num' => ['zan_num','id'],
    ];
}