<?php
namespace app\ebcms\validate;

use think\Validate;

class Extendfield extends Validate
{

    protected $rule = [
        'category_id|分类' => 'require',
        'group|分组' => 'require|max:10',
        'title|标题' => 'require|max:10',
        'name|名称' => 'require|max:20',
        'type|类型' => 'require',
        'config|配置' => 'array',
        'id|ID' => 'require',
    ];

    protected $scene = [
        'add' => ['category_id', 'group', 'title', 'name', 'type'],
        'edit' => ['group', 'title', 'name', 'type', 'id'],
        'config' => ['config', 'id'],
    ];
}