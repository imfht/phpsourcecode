<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
 * 验证器
 */
class Article extends ValidateBase
{
    
    // 验证规则
    protected $rule = [
        'title'  => 'require|length:6,32',
  
       
    ];

    // 验证提示
    protected $message = [
        'title.require'    => '标题不能为空',
        'title.length'     => '标题长度为6-32个字符之间',

    ];

    // 应用场景
    protected $scene = [
       	'edit'  =>  ['title'],
        'add'  =>  ['title'],
    ];
}
