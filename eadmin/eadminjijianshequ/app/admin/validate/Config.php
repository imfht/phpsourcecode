<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

/**
 * 配置验证器
 */
class Config extends AdminBase
{

    // 验证规则
    protected $rule = [

        'name'  => 'require|unique:config',
        'title' => 'require',
        'sort'  => 'require|number',
    ];

    // 验证提示
    protected $message = [

        'name.require'  => '配置名称不能为空',
        'name.unique'   => '配置名称已存在',
        'title.require' => '配置标题不能为空',
        'sort.require'  => '排序值不能为空',
        'sort.number'   => '排序值必须为数字'
    ];

    // 应用场景
    protected $scene = [

        'add'  => ['name', 'title', 'sort'],
        'edit' => ['name', 'title', 'sort'],
    ];

}
