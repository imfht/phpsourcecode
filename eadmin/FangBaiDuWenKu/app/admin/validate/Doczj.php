<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

/**
 * 验证器
 */
class Doczj extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name'  => 'require|unique:doczj',
    ];
    
    // 验证提示
    protected $message  =   [
        
        'name.require'    => '专辑名不能为空',
        'name.unique'     => '专辑名已存在',

    ];

    // 应用场景
    protected $scene = [
    		'edit'  =>  ['name'],
            'add'  =>  ['name'],
    ];
    
}
