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
class User extends Validate{
	protected $rule = [
		'uid'       => 'require',
		'oldpassword' => 'require|checkOldpaswd',
		'username'  =>  'require|unique:member|alphaDash',
		'email'     => 'email|unique:member',
		'password'  => 'require|min:8',
		'repassword' =>'require|confirm:password'
	];

	protected $message  =   [
		'uid.require' => '用户UID必须',
		'oldpassword.require' => '旧密码必须',
		'oldpassword.checkOldpaswd' => '旧密码错误',
		'username.require' => '用户名称必须',
		'username.unique' => '该用户名已存在',
		'username.alphaDash' => '用户名只能使用字母、数字、_、-',
		'password.min' => '密码不能小于8位',
		'repassword.require' => '确认密码不能为空',
		'repassword.confirm' => '确认密码和密码必须相同',
		'email.email' => '邮箱格式错误',
		'email.unique' => '邮箱已存在',
	];

	protected $scene = [
		'adminadd'  =>  ['username', 'email', 'password', 'repassword'],
		'adminedit'  =>  ['username', 'email'],
		'admineditpwd'    => ['uid', 'password', 'repassword', 'oldpassword']
	];

	protected function checkOldpaswd($value, $rule, $data){
		if(!$data['uid']){
			return false;
		}
		$user = Member::find($data['uid']);
		if (md5($value . $user['salt']) === $user['password']) {
			return true;
		}
		return false;
	}
}