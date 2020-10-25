<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace addon\superlinks\validate;

use app\common\validate\ValidateBase;

/**
 * 会员验证器
 */
class superlinks extends ValidateBase
{
    
    // 验证规则
    protected $rule = [
        'title'  => 'require|length:5,30',
  
       
    ];

    // 验证提示
    protected $message = [
        'title.require'    => '名称不能为空',
        'title.length'     => '名称长度为5-30个字符之间',
 
    ];

    // 应用场景
    protected $scene = [
       	'edit'  =>  ['title'],
        'add'  =>  ['title'],
    ];
}
