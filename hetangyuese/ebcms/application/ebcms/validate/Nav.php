<?php
namespace app\ebcms\validate;

use think\Validate;

class Nav extends Validate
{
    protected $rule = [
        'title|标题' => 'require|max:80',
    ];

    protected $scene = [
        'add' => ['title'],
        'edit' => ['title'],
    ];
}