<?php

namespace app\merchant\controller;

use \think\Db;
use \think\Session;

class Index
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		if(!Db::name('store')->where('merchant_id', '=', $this->merchant['merchant_id'])->count()) {
			$store_id = model('Store')->get_one_store($this->merchant['merchant_id']);
		}
		//$beg_time = gstime('Sunday -6 day', _time());
		//$end_time = $beg_time + 60 * 60 * 24 * 7;
		$end_time = gstime(gsdate('Y-m-d')) + 60 * 60 * 24;
		$beg_time = $end_time - 60 * 60 * 24 * 7;
		$where = [];
		$where['trade_status'] = ['IN', ['SUCCESS', 'TRADE_SUCCESS']];
		$where['time_create'] = ['BETWEEN', [$beg_time, $end_time]];
		$list = Db::name('trade')
			->where($where)
			->where('merchant_id', '=', $this->merchant['merchant_id'])
			->field('trade_gate, total_amount, time_create')
			->select();
		$_week = [];
		$cur_time = $beg_time;
		for($i=0; $i<7; $i++) {
			$_week[] = gsdate('m-d', $cur_time);
			$cur_time += 60 * 60 * 24;
		}
		$_count = [
			'today' => 0,
			'alipay' => 0,
			'weixin' => 0,
		];
		$_amount = [
			'today' => 0,
			'alipay' => 0,
			'weixin' => 0,
		];
		$_week_count = [
			'alipay' => [],
			'weixin' => [],
		];
		$_week_amount = [
			'alipay' => [],
			'weixin' => [],
		];
		foreach($list as $val) {
			if(gsdate('m-d') == gsdate('m-d', $val['time_create'])) {
				$_count['today']++;
				$_amount['today'] += $val['total_amount'];
				$_amount[$val['trade_gate']] += $val['total_amount'];
			}
			foreach($_week as $_date) {
				if(!isset($_week_count[$val['trade_gate']][$_date]) || !isset($_week_amount[$val['trade_gate']][$_date])) {
					$_week_count[$val['trade_gate']][$_date] = 0;
					$_week_amount[$val['trade_gate']][$_date] = 0;
				}
				if($_date == gsdate('m-d', $val['time_create'])) {
					$_week_count[$val['trade_gate']][$_date]++;
					$_week_amount[$val['trade_gate']][$_date] += $val['total_amount'];
				}
			}
		}
		$_week = json_encode($_week);
		$_week_count['alipay'] = json_encode(array_values($_week_count['alipay']));
		$_week_count['weixin'] = json_encode(array_values($_week_count['weixin']));
		foreach($_week_amount['alipay'] as $key => $val) {
			$_week_amount['alipay'][$key] = number($val);
		}
		foreach($_week_amount['weixin'] as $key => $val) {
			$_week_amount['weixin'][$key] = number($val);
		}
		$_week_amount['alipay'] = json_encode(array_values($_week_amount['alipay']));
		$_week_amount['weixin'] = json_encode(array_values($_week_amount['weixin']));

		include \befen\view();
	}

	public function cpasswd()
	{
		if(request()->isPost()) {
			$password = input('post.password');
			$new_password = input('post.new_password');
			$renew_password = input('post.renew_password');
			if($password != authcode($this->merchant['password'], 'DECODE')) {
				return make_json(0, '原密码错误');
			}
			if(!$new_password) {
				return make_json(0, '请输入新密码!');
			}
			if($new_password != $renew_password) {
				return make_json(0, '两次密码输入不一致');
			}
			Db::name('merchant')->where('merchant_id', '=', $this->merchant['merchant_id'])->update([
				'password' => authcode($new_password, 'ENCODE'),
			]);
			Session::delete('merchant');
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@merchant', ['merchant_id' => $this->merchant['merchant_id']]);
			/* HjSync */
			return make_json(1, '密码修改成功、前往登录页面');
		}
		include \befen\view();
	}

	public function get_id_authcode($expires = 0)
	{
		return make_json(1, 'ok', ['merchant_id' => authcode($this->merchant['merchant_id'], 'ENCODE', '', $expires)]);
	}

}

