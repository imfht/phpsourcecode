<?php

namespace app\merchant\controller;

use \think\Db;
use \think\Cookie;
use \app\common\Pay;

class Cash
{

	public $merchant;

	public function __construct()
	{
		if(in_array(request()->action(), ['window', 'login', 'logout', ])) {
			Cookie::prefix('store_person_');
		} else {
			$this->merchant = model('Merchant')->checkLoginMerchant();
		}
	}

	public function index()
	{
		$this->store_person = Cookie::get('cash');
		if(!$this->store_person) {
			return \befen\redirect(url('merchant/cash/login'));
		} else {
			return \befen\redirect(url('merchant/cash/window'));
		}
	}

	public function login()
	{
		if(request()->isPost()) {
			$per_phone = input('post.per_phone');
			$password = input('post.password');
			if(!$per_phone || !$password) {
				return make_json(0, '店员手机或密码不能为空');
			}
			$value = Db::name('store_person')->where('per_phone', '=', $per_phone)->find();
			if(!$value) {
				return make_json(0, '店员不存在');
			}
			if($password != authcode($value['password'], 'DECODE')) {
				return make_json(0, '店员密码错误');
			}
			if(0 == $value['status']) {
				return make_json(0, '当前店员不可用');
			}
			Cookie::forever('cash', $value);
			return make_json(1, '登录成功');
		}
		include \befen\view();
	}

	public function logout()
	{
		Cookie::delete('cash');
		return \befen\redirect(url('merchant/cash/login'));
	}

	public function window()
	{
		$this->store_person = Cookie::get('cash');
		if(!$this->store_person) {
			return \befen\redirect(url('merchant/cash/login'));
		}
		$this->merchant = Pay::merchant($this->store_person['merchant_id']);
		include \befen\view();
	}

}

