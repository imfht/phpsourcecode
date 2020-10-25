<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
 * 验证器
 */
class Comment extends ValidateBase
{
    
    // 验证规则
    protected $rule = [
        'content'  => 'require',
    
    		
       
    ];

    // 验证提示
    protected $message = [
        'content.require'    => '内容不能为空',
 
    ];

 
    
    
    // 应用场景
    protected $scene = [
       	'edit'  =>  ['content'],
        'add'  =>  ['content'],
    ];
}
