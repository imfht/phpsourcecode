<?php

namespace app\merchant\controller;

use \think\Db;

class Trade
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		$where = [];
		$where['t.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$where['t.trade_status'] = ['IN', ['CLOSED', 'SUCCESS', 'TRADE_CLOSED', 'TRADE_SUCCESS']];
		if(input('param.trade_gate')) {
			$where['trade_gate'] = ['=', input('param.trade_gate')];
		}
		if(input('param.store_id')) {
			$where['t.store_id'] = ['=', input('param.store_id')];
		}
		if(input('param.person_id')) {
			$where['t.person_id'] = ['=', input('param.person_id')];
		}
		if(input('param.out_trade_no')) {
			$where['t.out_trade_no'] = ['=', input('param.out_trade_no')];
		}
		if(input('param.time_create')) {
			$time_create_range = explode('~', input('param.time_create'));
			$time_create_range = array_map(function($v){
				return gstime(trim($v));
			}, $time_create_range);
			$time_create_range[1] += 86400;
			$where['t.time_create'] = ['BETWEEN TIME', $time_create_range];
		}
		$object = Db::name('trade')
			->alias('t')
			->join('store s', 's.store_id = t.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
			->where($where)
			->field('t.*, s.store_name, m.merchant_name')
			->order('trade_id', 'DESC')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		$persons = Db::name('store_person')->where('merchant_id', '=', $this->merchant['merchant_id'])->field('person_id, per_name')->select();
		include \befen\view();
	}

	public function detail($out_trade_no = '')
	{
		$where = [];
		$where['t.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$where['t.out_trade_no'] = ['=', $out_trade_no];
		$value = Db::name('trade')
			->alias('t')
			->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
			->join('store s', 's.store_id = t.store_id', 'LEFT')
			->join('store_person sp', 'sp.person_id = t.person_id', 'LEFT')
			->join('store_device sd', 'sd.device_id = t.device_id', 'LEFT')
			->join('mch_bill mb', 'mb.out_trade_no = t.out_trade_no', 'LEFT')
			->where($where)
			->field('t.*, m.merchant_name, s.store_name, sp.per_name, sd.SN, mb.id as is_mch_bill')
			->find();
		include \befen\view();
	}

	public function refund($out_trade_no = '')
	{
		if(!$out_trade_no) {
			$out_trade_no = input('post.out_trade_no');
		}
		$where = [];
		$where['t.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$where['t.out_trade_no'] = ['=', $out_trade_no];
		$value = Db::name('trade')
			->alias('t')
			->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
			->join('store s', 's.store_id = t.store_id', 'LEFT')
			->join('store_person sp', 'sp.person_id = t.person_id', 'LEFT')
			->join('store_device sd', 'sd.device_id = t.device_id', 'LEFT')
			->where($where)
			->field('t.*, m.merchant_name, s.store_name, sp.per_name, sd.SN')
			->find();
		if(request()->isPost()) {
			if(input('post.password') != authcode($this->merchant['password'], 'DECODE')) {
				return make_json(0, '密码错误!');
			}
			$merchant = \app\common\Pay::merchant($this->merchant['merchant_id']);
			return \app\pay\controller\Index::refund($merchant, $out_trade_no);
		}
		include \befen\view();
	}

}

