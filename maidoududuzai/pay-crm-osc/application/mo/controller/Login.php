<?php

namespace app\mo\controller;

use \think\Db;
use \think\Session;

class Login
{
	
	public $agent;

	public function __construct()
	{

	}

	public function index()
	{
		if(model('Agent')->getLoginAgent()) {
			return \befen\redirect(url('index/index'));
		}
		include \befen\view();
	}

	public function doLogin()
	{
		$agent_no = input('post.agent_no');
		$password = input('post.password');
		if(!$agent_no || !$password) {
			return make_json(0, '代理编号或代理密码不能为空');
		}
		$value = model('Agent')->where(['per_phone|agent_no' => ['=', $agent_no]])->find();
		if(!$value) {
			return make_json(0, '代理商不存在');
		}
		$value = $value->toArray();
		if($password != authcode($value['password'], 'DECODE')) {
			return make_json(0, '代理商密码错误');
		}
		if(0 == $value['agent_status']) {
			return make_json(0, '当前用户被禁用');
		}
		Session::set('agent', $value);
		$value['Login_IP'] = get_ipaddr();
		$value['Login_Time'] = _time();
		model('Agent')->allowField(true)->save([
			'Login_IP' => $value['Login_IP'],
			'Login_Time' => $value['Login_Time'],
		], ['agent_no' => $agent_no]);
		return make_json(1, '登录成功、前往管理页面');
	}

	public function doLogout()
	{
		Session::delete('agent');
		return \befen\redirect(url('login/index'));
	}

}

