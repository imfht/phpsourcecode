<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

/**
 * 会员验证器
 */
class User extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'username'  => 'require|unique:user',
        'password'  => 'require|confirm|length:6,20',
        'usermail'     => 'require|email|unique:user',
    ];
    
    // 验证提示
    protected $message  =   [
        
        'username.require'    => '用户名不能为空',
        'username.unique'     => '用户名已存在',
        'password.require'    => '密码不能为空',
        'password.confirm'    => '两次密码不一致',
        'password.length'     => '密码长度为6-20字符',
        'usermail.require'       => '邮箱不能为空',
        'usermail.email'         => '邮箱格式不正确',
        'usermail.unique'        => '邮箱已存在',
    ];

    // 应用场景
    protected $scene = [
    		'edit'  =>  ['username','usermail'],
        'add'  =>  ['username','password','usermail'],
    ];
    
}
