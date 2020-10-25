<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

/**
 * 权限组验证器
 */
class AuthGroup extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name' => 'require',
    ];

    // 验证提示
    protected $message  =   [
        
        'name.require' => '权限组名称不能为空',
    ];

    // 应用场景
    protected $scene = [
        
        'add'  => ['name'],
        'edit' => ['name'],
    ];
    
}
