<?php
namespace app\common\validate;

use think\Validate;


class Reg extends Validate
{
    //定义验证规则
    protected $rule = [
        'username|用户名'   => 'require|chsDash|length:2,25|unique:memberdata',
		'password|密码'   => 'require|length:5,20',
        'password2|确认密码'   => 'require|confirm:password',
        'email|邮箱'  => 'email',
        'captcha|验证码'  => 'captcha',
    ];
    
    //定义验证提示
    protected $message = [            
            'username.length' => '用户名长度不能小于2位',
            'password.length' => '密码长度必须是5位以上',
            'password2.confirm' => '确认密码与密码不一样',
    ];
    
    //定义验证场景
    protected $scene = [
            'password'  =>  ['password'],
            'username'  =>  ['username'],
            'email'  =>  ['email'],
            'captcha'  =>  ['captcha'],
    ];
    
}
