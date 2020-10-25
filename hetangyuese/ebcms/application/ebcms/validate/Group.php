<?php
namespace app\ebcms\validate;

use think\Validate;

class Group extends Validate
{

    protected $rule = [
        'group|分组' => 'require|max:25',
        'title|标题' => 'require|max:80',
        'remark|备注' => 'max:255',
    ];

    protected $scene = [
        'add' => ['group', 'title', 'remark'],
        'edit' => ['group', 'title', 'remark'],
    ];
}