<?php

namespace app\admin\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule = [
        'cid'   => 'require',
        'title' => 'require',
    ];

    protected $message = [
        'cid.require'   => '请选择所属分类',
        'title.require' => '标题不能为空',
    ];
}
