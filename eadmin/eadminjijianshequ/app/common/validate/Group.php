<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
 * 验证器
 */
class Group extends ValidateBase
{

    // 验证规则
    protected $rule = [
        'name' => 'require|unique:group',

    ];

    // 验证提示
    protected $message = [
        'name.require' => '名称不能为空',
        'name.unique'  => '名称不能重复',


    ];

    // 应用场景
    protected $scene = [
        'edit' => ['name'],
        'add'  => ['name'],
    ];

}
