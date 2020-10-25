<?php

namespace app\mp\controller;

use \think\Db;
use \think\Cookie;

class Login
{

	public $person;

	public function __construct()
	{

	}

	public function index()
	{
		if(model('StorePerson')->getLoginPerson()) {
			return \befen\redirect(url('index/index'));
		}
		include \befen\view();
	}

	public function doLogin()
	{
		$username = input('post.username');
		$password = input('post.password');
		if(!$username || !$password) {
			return make_json(0, '用户名或密码不能为空');
		}
		$value = model('StorePerson')->where(['per_phone' => ['=', $username]])->find();
		if(!$value) {
			return make_json(0, '店员不存在');
		}
		$value = $value->toArray();
		if($password != authcode($value['password'], 'DECODE')) {
			return make_json(0, '店员密码错误');
		}
		if(0 == $value['status']) {
			return make_json(0, '当前店员被禁用');
		}
		Cookie::set('person', $value);
		return make_json(1, '登录成功、前往管理页面');
	}

	public function doLogout()
	{
		Cookie::delete('person');
		return \befen\redirect(url('login/index'));
	}

}

