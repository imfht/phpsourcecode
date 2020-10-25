<?php

namespace app\admin\validate;

/**
 * 验证器
 */
class Topiccate extends AdminBase
{

    // 验证规则
    protected $rule = [

        'name' => 'require|unique:groupcate',
    ];

    // 验证提示
    protected $message = [

        'name.require' => '分类名不能为空',
        'name.unique'  => '分类名已存在',

    ];

    // 应用场景
    protected $scene = [
        'edit' => ['name'],
        'add'  => ['name'],
    ];

}
