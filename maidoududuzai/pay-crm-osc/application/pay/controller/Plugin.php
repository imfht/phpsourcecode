<?php

namespace app\pay\controller;

use \think\Db;
use \app\common\Pay;
use \app\common\PayAction;

class Plugin
{

	public $merchant = [];

	public $person = [];

	public $errNo = 0;
	public $errMsg = null;

	public function __construct()
	{

		$this->store_id = input('post.store_id/d', 0);
		$this->person_id = input('post.person_id/d', 0);
		if(empty($this->store_id) || empty($this->person_id)) {
			$this->merchant = [];
			$this->errMsg = '缺少公共参数';
		} else {
			$this->merchant = Pay::merchant($merchant_id, ['person_id' => $this->person_id]);
			if($this->store_id != $this->merchant['store_person']['store_id']) {
				$this->merchant = [];
				$this->errMsg = '登录验证失败';
			}
		}

	}

	/**
	 * 获取商户交易号
	 */
	public function index($prefix = 'P')
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		if(!preg_match('/^[A-Z]{1,2}$/', $prefix)) {
			return make_json(0, '前缀格式错误');
		}
		$length = strlen($prefix);
		$out_trade_no = preg_replace('/\d{' . $length . '}$/', '', get_order_number($prefix));
		$contents = ['out_trade_no' => $out_trade_no];
		PayAction::log(JSON($contents));
		return make_json(1, 'ok', $contents);
	}

	/**
	 * 店员登录
	 * @param String $username
	 * @param String $password
	 */
	public function login()
	{
		PayAction::log(request()->method() . ' ' . request()->url());
		PayAction::log(input('post.'));
		$username = input('post.username/s');
		if(empty($username)) {
			return make_json(0, '缺少参数 [username]');
		}
		$password = input('post.password/s');
		if(empty($password)) {
			return make_json(0, '缺少参数 [password]');
		}
		$person = $this->get_person(['per_phone' => $username]);
		if(!$person || $password != authcode($person['password'], 'DECODE')) {
			return make_json(0, '登录失败 [login error]');
		}
		$person['person_token'] = authcode(authcode($person['password'], 'DECODE'), 'ENDODE');
		unset($person['password']);
		PayAction::log($person);
		return make_json(1, 'ok', $person);
	}

	/**
	 * 密码验证
	 * @param String $password
	 */
	public function check()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$password = input('post.password');
		if($password == authcode($this->person['password'], 'DECODE')) {
			return make_json(1, 'ok');
		} else {
			return make_json(0, '密码错误');
		}
	}

	/**
	 * 获取店员信息
	 * @param Array $field_person
	 */
	public function get_person($field = [])
	{
		$where = [];
		foreach($field as $key => $val) {
			$where['sp.'.$key] = ['=', $val];
		}
		$person = Db::name('store_person')
			->alias('sp')
			->join('merchant m', 'm.merchant_id = sp.merchant_id', 'LEFT')
			->join('store s', 's.store_id = sp.store_id', 'LEFT')
			->join('store_device sd', 'sd.merchant_id = sp.merchant_id', 'LEFT')
			->where($where)
			->field('sp.person_id, sp.per_name, sp.per_phone, sp.status, sp.manager, sp.openid, sp.password, m.merchant_id, m.merchant_no, m.merchant_name, m.merchant_shortname, s.store_id, s.store_name')
			->find();
		if(!$person) {
			return [];
		} else {
			if(empty($person['merchant_shortname'])) {
				$person['merchant_shortname'] = $person['merchant_name'];
			}
			unset($person['agent_id']);
			unset($person['remarks']);
			unset($person['time_create']);
			unset($person['time_update']);
			return $person;
		}
	}

	/**
	 * 支付接口
	 * @param Float $total_amount
	 * @param String $auth_code
	 */
	public function pay()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$total_amount = input('post.total_amount/f', 0);
		if($total_amount == 0) {
			return make_json(0, '缺少参数 [total_amount]');
		}
		$auth_code = input('post.auth_code/s');
		if(empty($auth_code)) {
			return make_json(0, '缺少参数 [auth_code]');
		}
		$pay_client = Pay::client($auth_code);
		if(!$pay_client) {
			return make_json(0, '无法识别付款码', $auth_code);
		}
		$this->errMsg = Index::check_merchant($pay_client, $this->merchant);
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$out_trade_no = model('Trade')->_insert($this->merchant, $pay_client, 'bar_code', $total_amount);
		$sub_gate = $this->merchant['gate_' . $pay_client];
		if(!empty($sub_gate)) {
			if(empty($this->merchant[$sub_gate])) {
				return make_json(0, '支付通道未配置');
			}
			model('Trade')->_update($out_trade_no, ['sub_gate' => $sub_gate]);
		}
		$gate = $sub_gate ? $sub_gate : $pay_client;
		$PostData = [
			'out_trade_no' => $out_trade_no,
			'auth_code' => $auth_code,
			'total_amount' => $total_amount,
			'subject' => $this->merchant['merchant_name'],
			'body' => $this->merchant['merchant_name'],
			'spbill_create_ip' => get_ipaddr(),
		];
		$res = PayAction::gate($gate)->merchant($this->merchant)->pay($PostData);
		if(empty($sub_gate)) {
			$res = PayAction::result_filter($res);
		}
		if($res->status == 0) {
			if((isset($res->contents->code) && $res->contents->code == 10003) || (isset($res->contents->err_code) && $res->contents->err_code == 'USERPAYING')) {
				$res->status = 1;
				$res->message = 'query';
			}
		} else {
			$res->message = 'query';
		}
		if(isset($res->contents->bizCode)) {
			if($res->contents->bizCode === '0000' && isset($res->contents->transactionId)) {
				$res->message = 'query';
				model('Trade')->_update($out_trade_no, [
					'trade_no' => $res->contents->transactionId
				]);
			}
			if($res->contents->bizCode === '2002' || (isset($res->contents->tranSts) && $res->contents->tranSts === 'PAYING')) {
				$res->message = 'query';
			}
		}
		$contents = ['out_trade_no' => $out_trade_no];
		$res->contents = $res->contents ? $res->contents : [];
		foreach($res->contents as $key => $value) {
			if(!isset($contents[$key])) {
				$contents[$key] = $value;
			}
		}
		$res->contents = $contents;
		return make_json($res->status, $res->message, $res->contents);
	}

	/**
	 * 查询接口
	 * @param String $out_trade_no
	 */
	public function query()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return JSON(Index::query($this->merchant, input('post.out_trade_no/s')));
	}

	/**
	 * 退款接口
	 * @param String $out_trade_no
	 */
	public function refund()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		return JSON(Index::refund($this->merchant, input('post.out_trade_no/s')));
	}

	/**
	 * 订单列表
	 * @param Int $page
	 * @param String $trade_gate
	 * @param String $time_create `2011-01-02 ~ 2012-02-03`
	 */
	public function order()
	{
		if($this->errMsg) {
			return make_json(0, $this->errMsg);
		}
		$where = [];
		if($this->merchant['store_person']['manager']) {
			$where['t.store_id'] = ['=', $this->store_id];
		} else {
			$where['t.person_id'] = ['=', $this->person_id];
		}
		$where['t.trade_status'] = ['IN', ['CLOSED', 'SUCCESS', 'TRADE_CLOSED', 'TRADE_SUCCESS']];
		if(input('post.trade_gate')) {
			$where['trade_gate'] = ['=', input('post.trade_gate')];
		}
		if(input('post.trade_type')) {
			$where['trade_type'] = ['=', input('post.trade_type')];
		}
		if(input('post.time_create')) {
			$time_create_range = explode('~', input('post.time_create'));
			$time_create_range = array_map(function($v){
				return gstime(trim($v));
			}, $time_create_range);
			$time_create_range[1] += 86400;
			$where['t.time_create'] = ['BETWEEN TIME', $time_create_range];
		}
		$object = Db::name('trade')
			->alias('t')
			->join('store s', 's.store_id = t.store_id', 'LEFT')
			->where($where)
			->order('trade_id', 'DESC')
			->field('t.*')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$last_page = $array['last_page'];
		$current_page = $array['current_page'];
		foreach($list as $k => $v) {
			foreach($v as $key => $val) {
				if(!in_array($key, ['store_id', 'person_id', 'trade_gate', 'trade_type', 'out_trade_no', 'total_amount', 'trade_status', 'time_create'])) {
					unset($list[$k][$key]);
				}
				if($key == 'trade_status') {
					$list[$k][$key] = preg_replace('/^TRADE_/', '', $val);
				}
			}
		}
		return make_json(1, 'ok', [
			'total' => $array['total'],
			'list' => $list,
			'per_page' => $array['per_page'],
			'last_page' => $array['last_page'],
			'current_page' => $array['current_page'],
		]);
	}

}

