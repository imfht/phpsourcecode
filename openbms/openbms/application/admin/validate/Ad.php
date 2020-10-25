<?php

namespace app\admin\validate;

use think\Validate;

class Ad extends Validate
{
    protected $rule = [
        'image'    => 'require',
        'category' => 'require',
        'name'     => 'require',
    ];

    protected $message = [
        'image.require'    => '请上传广告图片',
        'category.require' => '请选择所属分类',
        'name.require'     => '名称不能为空',
    ];
}
