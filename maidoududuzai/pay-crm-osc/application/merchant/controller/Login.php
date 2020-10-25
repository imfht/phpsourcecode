<?php

namespace app\merchant\controller;

use \think\Db;
use \think\Session;

class Login
{

	public function __construct()
	{

	}

	public function index()
	{
		if(model('Merchant')->getLoginMerchant()) {
			redirect(url('merchant/index/index'));
		}
		include \befen\view();
	}

	public function console()
	{
		if(!model('Admin')->getLoginAdmin()) {
			return 'Access Denied';
		}
		$merchant_id = authcode(input('param.merchant_id'), 'DECODE');
		$value = model('Merchant')->get_one($merchant_id);
		if(!$value) {
			return 'Access Denied';
		}
		Session::set('merchant', $value);
		return \befen\redirect(url('merchant/index/index'));
	}

	public function doLogin()
	{
		$merchant_no = input('post.merchant_no');
		$password = input('post.password');
		if(!$merchant_no || !$password) {
			return make_json(0, '商户编号或商户密码不能为空');
		}
		$value = model('Merchant')->where(['per_phone|merchant_no' => ['=', $merchant_no]])->find();
		if(!$value) {
			return make_json(0, '商户不存在');
		}
		$value = $value->toArray();
		if($password != authcode($value['password'], 'DECODE')) {
			return make_json(0, '商户密码错误');
		}
		if(0 == $value['status']) {
			return make_json(0, '当前商户被禁用');
		}
		Session::set('merchant', $value);
		$value['Login_IP'] = get_ipaddr();
		$value['Login_Time'] = _time();
		model('Merchant')->allowField(true)->save([
			'Login_IP' => $value['Login_IP'],
			'Login_Time' => $value['Login_Time'],
		], ['merchant_no' => $merchant_no]);
		return make_json(1, '登录成功、前往管理页面');
	}

	public function doLogout()
	{
		Session::delete('merchant');
		return make_json(0, '退出成功、前往登录页面');
	}

}

