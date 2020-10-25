<?php
namespace app\ebcms\validate;

use think\Validate;

class App extends Validate
{

    protected $rule = [
        'title|标题' => 'require|max:80',
        'name|名称' => 'require',
        'version|版本' => 'require',
    ];

    protected $scene = [
        'add' => ['title', 'name', 'version'],
        'edit' => ['title', 'name', 'version'],
    ];
}