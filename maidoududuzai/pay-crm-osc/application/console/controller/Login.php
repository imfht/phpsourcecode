<?php

namespace app\console\controller;

use \think\Db;
use \think\Session;

class Login
{

	public function __construct()
	{

	}

	public function index()
	{
		if(model('Admin')->getLoginAdmin()) {
			return \befen\redirect(url('console/index/index'));
		}
		include \befen\view();
	}

	public function doLogin()
	{
		$username = input('post.username');
		$password = input('post.password');
		if(!$username || !$password) {
			return make_json(0, '用户或密码不能为空');
		}
		$value = model('Admin')->get_one(['username' => $username]);
		if(!$value) {
			return make_json(0, '用户不存在');
		}
		if($password != authcode($value['password'], 'DECODE')) {
			return make_json(0, '用户密码错误');
		}
		if(0 == $value['status']) {
			return make_json(0, '当前用户被禁用');
		}
		Session::set('admin', $value);
		$value['Login_IP'] = get_ipaddr();
		$value['Login_Time'] = _time();
		model('Admin')->allowField(true)->save([
			'Login_IP' => $value['Login_IP'],
			'Login_Time' => $value['Login_Time'],
		], ['username' => $username]);
		return make_json(1, '登录成功、前往管理页面');
	}

	public function doLogout()
	{
		Session::delete('admin');
		return make_json(0, '退出成功、前往登录页面');
	}

}

