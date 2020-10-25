<?php
namespace app\ebcms\validate;

use think\Validate;

class Config extends Validate
{

    protected $rule = [
        'group|分组' => 'require|max:25',
        'category_id|分类' => 'require',
        'pid|父级' => 'require',
        'title|标题' => 'require|max:80',
        'name|名称' => 'require|max:40',
        'form|表单' => 'require',
        'config|配置' => 'array',
    ];

    protected $scene = [
        'add' => ['group', 'pid', 'category_id', 'title', 'name', 'form'],
        'edit' => ['group', 'title', 'name', 'form'],
    ];
}