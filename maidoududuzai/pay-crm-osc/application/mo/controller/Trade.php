<?php

namespace app\mo\controller;

use \think\Db;

class Trade
{

	public $agent;

	public function __construct()
	{
		$this->agent = model('Agent')->checkLoginAgent();
	}

	public function index()
	{
		if(input('param.merchant_id')){
			$where = [];
			$where['t.agent_id'] = ['=', $this->agent['agent_id']];
			$where['t.trade_status'] = ['IN', ['CLOSED', 'SUCCESS', 'TRADE_CLOSED', 'TRADE_SUCCESS']];
			if(input('param.trade_gate')) {
				$where['trade_gate'] = ['=', input('param.trade_gate')];
			}
			if(input('param.time_create')) {
				$time_create = input('param.time_create');
				$where['t.time_create'] = ['BETWEEN TIME', [$time_create.' 00:00:00', $time_create.' 23:59:59']];
			}
			$merchant_id = input('param.merchant_id');
			if(empty($merchant_id)){
				return make_json('0', '参数merchant_id必需');
			}
			$where['m.merchant_id'] = input('param.merchant_id/d');

			if(request()->isAjax()){
				$object = Db::name('trade')
					->alias('t')
					->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
					->field('t.*, m.merchant_name')
					->where($where)
					->order('trade_id', 'DESC')
					->paginate(15, false, ['query' => request()->param()])
					->each(function($item, $key){
						$item['time_create'] = gsdate('Y-m-d H:i:s', $item['time_create']);
						return $item;
					});
				$array = $object->toArray();
				$total = $array['total'];
				$list = $array['data'];
				$per_page = $array['per_page'];
				$current_page = $array['current_page'];
				$last_page = $array['last_page'];
				$pagenav = $object->render();
				$data = [
					'list' => $list,
					'total' => $total,
					'last_page' => $last_page
				];
				//总计
				if(!empty($where['t.time_create'])){
					$where['t.trade_status'] = ['IN', ['SUCCESS', 'TRADE_SUCCESS']];
					$sum = Db::name('trade')
					->alias('t')
					->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
					->field('t.*, m.merchant_name')
					->where($where)
					->sum('total_fee');
					$sum = $sum / 100;
					$data['sum'] = $sum;
				}
				return make_json(1, 'ok', $data);
			}
			//当前选择商户
			$selected_merchant = Db::name('merchant')->where('merchant_id', '=', $where['m.merchant_id'])->field('merchant_id, merchant_name')->find();
			include \befen\view();
		} else {
			include \befen\view('Trade_filter');
		}
	}

	public function detail($trade_id){
		$where = [
			't.agent_id' => ['=', $this->agent['agent_id']],
			't.trade_id' => $trade_id
		];
		$value = Db::name('trade')
		->alias('t')
		->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
		->join('store s', 's.store_id = t.store_id', 'LEFT')
		->join('store_person sp', 'sp.person_id = t.person_id', 'LEFT')
		->join('store_device sd', 'sd.device_id = t.device_id', 'LEFT')
		->field('t.*, s.store_name, m.merchant_name')
		->where($where)
		->field('t.*, m.merchant_name, s.store_name, sp.per_name, sd.SN')
		->find();
		$value['trade_status'] = model('Trade')::getStatus($value['trade_status']);
		include \befen\view();
	}

	public function profit()
	{
		if(input('param.merchant_id')){
			$where = [];
			$where['tp.agent_id'] = ['=', $this->agent['agent_id']];
			if(input('param.merchant_name')) {
				$where['m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
			if(input('param.time_create')) {
				$time_create = input('param.time_create');
				$where['tp.time_create'] = ['BETWEEN TIME', [$time_create.' 00:00:00', $time_create.' 23:59:59']];
			}
			$where['m.merchant_id'] = input('param.merchant_id');
			if(request()->isAjax()){
				$object = Db::name('trade_profit')
					->alias('tp')
					->join('trade t', 't.out_trade_no = tp.out_trade_no', 'LEFT')
					->join('merchant m', 'm.merchant_id = tp.merchant_id', 'LEFT')
					->join('agent a', 'a.agent_id = m.agent_id', 'LEFT')
					->field('tp.*, t.time_create as trade_create, t.trade_gate, t.total_amount, a.agent_no, a.agent_name, m.merchant_no, m.merchant_name')
					->where($where)
					->order('tp.id', 'DESC')
					->paginate(20, false, ['query' => request()->param()])
					->each(function($item, $key){
						$item['time_status_text'] = $item['time_status'] ? '已结算' : '未结算';
						$item['time_create'] = gsdate('Y-m-d H:i', $item['time_create']);
						return $item;
					});
				$array = $object->toArray();
				$total = $array['total'];
				$list = $array['data'];
				$per_page = $array['per_page'];
				$current_page = $array['current_page'];
				$last_page = $array['last_page'];
				$data = [
					'list' => $list,
					'total' => $total,
					'last_page' => $last_page
				];
				return make_json(1, 'ok', $data);
			}
			$selected_merchant = Db::name('merchant')->where('merchant_id', '=', $where['m.merchant_id'])->field('merchant_id, merchant_name')->find();
			include \befen\view();
			return;
		}
		include \befen\view('Trade_profit_filter');
	}
}

