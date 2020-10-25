<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 创建管理员验证器
 * @auth zhanghd <zhanghd1987@foxmail.com>
 */
class AuthMember extends Validate
{	
    protected $rule = [
        'username'  =>  'require|max:16|unique:auth_member|alphaDash',
		'nickname'	=>	'require|unique:auth_member',
		'password'	=>	'require|min:6|max:16',
		'phone'		=>	'require',
		'email'		=>	'require|email',
    ];

    protected $message = [
        'username.require'  =>  '登录名不能为空',
		'username.max'		=>	'登录名长度不能大于16位',
        'username.alphaDash'=>  '登录名必须为字母和数字，下划线(_)及破折号(-)',
		'username.unique'	=>	'登录名已经存在',
		'nickname.require'	=>	'昵称不能为空',
		'nickname.unique'	=>	'昵称已经存在',
		'password.require'	=>	'密码不能为空',
		'password.min'		=>	'密码长度范围在6-16位',
		'password.max'		=>	'密码长度范围在6-16位',
		'phone.require'		=>	'电话号码不能为空',
		'email.require'		=>	'邮箱不能为空',
		'email.email'		=>	'邮箱格式不正确',
	];

    protected $scene = [
        'add'   =>  ['username','nickname','password','phone','email'],
		'edit'	=>	['phone','email'],
    ];
}
