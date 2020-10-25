<?php
namespace app\ebcms\validate;

use think\Validate;

class Extend extends Validate
{
    protected $rule = [
        'group|分组' => 'require',
        'title|标题' => 'require',
    ];

    protected $scene = [
        'add' => ['group', 'title'],
        'edit' => ['title'],
    ];
}