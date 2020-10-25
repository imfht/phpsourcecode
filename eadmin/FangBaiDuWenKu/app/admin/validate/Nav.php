<?php

namespace app\admin\validate;

/**
 * 验证器
 */
class Nav extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name'  => 'require',

    ];
    
    // 验证提示
    protected $message  =   [
        
        'name.require'    => '导航名称不能为空',
       

    ];

    // 应用场景
    protected $scene = [
    	'edit'  =>  ['name'],
        'add'  =>  ['name'],
    ];
    
}
