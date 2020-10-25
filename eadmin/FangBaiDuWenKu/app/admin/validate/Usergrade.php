<?php

namespace app\admin\validate;

/**
 * 会员等级验证器
 */
class Usergrade extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name'  => 'require|unique:usergrade',

    ];
    
    // 验证提示
    protected $message  =   [
        
        'name.require'    => '等级名称不能为空',
        'name.unique'     => '等级已存在',

    ];

    // 应用场景
    protected $scene = [
    		'edit'  =>  ['name'],
        'add'  =>  ['name'],
    ];
    
}
