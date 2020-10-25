<?php

namespace app\console\controller;

use \think\Db;

class Trade
{

	public $admin;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
	}
	
	public function index()
	{
		$where = [];
		$where['t.trade_status'] = ['IN', ['CLOSED', 'SUCCESS', 'TRADE_CLOSED', 'TRADE_SUCCESS']];
		if(input('param.trade_gate')) {
			$where['trade_gate'] = ['=', input('param.trade_gate')];
		}
		if(input('param.merchant_id')) {
			$where['m.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
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
			->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
			->where($where)
			->field('t.*, m.merchant_name')
			->order('trade_id', 'DESC')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		foreach($list as $key => $val) {
			if(!empty($val['sub_gate'])) {
				$class = '\\app\\common\\subpay\\' . ucfirst($val['sub_gate']);
				$list[$key]['sub_gate'] = $class::NAME;
			} else {
				$list[$key]['sub_gate'] = '直连';
			}
		}
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		include \befen\view();
	}

	public function detail($out_trade_no)
	{
		$where = [];
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
		include \befen\view();
	}

	public function profit()
	{
		$where = [];
		if(input('param.merchant_id')) {
			$where['s.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
		}
		if(input('param.time_create')) {
			$time_create_range = explode('~', input('param.time_create'));
			$time_create_range = array_map(function($v){
				return gstime(trim($v));
			}, $time_create_range);
			$time_create_range[1] += 86400;
			$where['t.time_create'] = ['BETWEEN TIME', $time_create_range];
		}
		$object = Db::name('trade_profit')
			->alias('tp')
			->join('trade t', 't.out_trade_no = tp.out_trade_no', 'LEFT')
			->join('agent a', 'a.agent_id = tp.agent_id', 'LEFT')
			->join('agent_level al', 'a.level_id = al.level_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = tp.merchant_id', 'LEFT')
			->field('tp.*, t.time_create as trade_create, t.trade_gate, t.total_amount, a.agent_no, a.agent_name, m.merchant_no, m.merchant_name, m.trade_rates, al.trade_rates as agent_rates')
			->where($where)
			->order('tp.id', 'DESC')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		include \befen\view();
	}

}

