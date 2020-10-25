<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

/**
 * API分组验证器
 */
class ApiGroup extends AdminBase
{

    // 验证规则
    protected $rule = [
        'name' => 'require|unique:api_group',
    ];

    // 验证提示
    protected $message = [
        'name.require' => '分组名称不能为空',
        'name.unique'  => '分组名称已经存在',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['name'],
    ];
}
