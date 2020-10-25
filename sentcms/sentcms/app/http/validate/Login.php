<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\http\validate;

use think\Validate;
use app\model\Member;

/**
 * 菜单验证
 */
class Login extends Validate{
	protected $rule = [
		'username'  =>  'require|alphaDash',
		'password'  => 'require|min:8',
	];

	protected $message  =   [
		'username.require' => '用户名称必须',
		'username.alphaDash' => '用户名只能使用字母、数字、_、-',
		'password.min' => '密码不能小于8位',
		'repassword.require' => '确认密码不能为空'
	];

	protected $scene = [
		'apiindex'  =>  ['username', 'password'],
	];
}