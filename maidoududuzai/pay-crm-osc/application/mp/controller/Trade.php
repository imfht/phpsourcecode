<?php

namespace app\mp\controller;

use \think\Db;

class Trade
{

	public $person;
	public $merchant;

	public function __construct()
	{
		$this->person = model('StorePerson')->checkLoginPerson();
		$this->merchant = Db::name('merchant')->where('merchant_id', '=', $this->person['merchant_id'])->field('merchant_id, merchant_name')->find();
	}

	public function index()
	{
		$where = [];
		//店长可选店员
		if($this->person['manager']) {
			if(input('param.person_id')){
				$where['t.person_id'] = ['=', input('param.person_id')];
			}else{
				$where['t.person_id'] = ['<>', 0];
				$where['s.store_id'] = $this->person['store_id'];
			}
		}else{
			$where['t.person_id'] = ['=', $this->person['person_id']];
		}
		$where['t.trade_status'] = ['IN', ['CLOSED', 'SUCCESS', 'TRADE_CLOSED', 'TRADE_SUCCESS']];
		if(input('param.trade_gate')) {
			$where['trade_gate'] = ['=', input('param.trade_gate')];
		}
		if(input('param.store_id')) {
			$where['t.store_id'] = ['=', input('param.store_id')];
		}
		if(input('param.out_trade_no')) {
			$where['t.out_trade_no'] = ['=', input('param.out_trade_no')];
		}
		if(input('param.time_create')) {
			$time_create = input('param.time_create');
			$where['t.time_create'] = ['BETWEEN TIME', [$time_create.' 00:00:00', $time_create.' 23:59:59']];
		}
		$object = Db::name('trade')
			->alias('t')
			->join('store_person sp', 'sp.person_id=t.person_id', 'LEFT')
			->join('store s', 'sp.store_id=s.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id=t.merchant_id', 'LEFT')
			->field('t.*, s.store_name, m.merchant_name')
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
			->join('store_person sp', 'sp.person_id = t.person_id', 'LEFT')
			->join('store s', 'sp.store_id = s.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
			->where($where)
			->sum('total_fee');
			$sum = $sum / 100;
			$data['sum'] = $sum;
		}
		//当前选择店员
		$selected_person = Db::name('store_person')->where('person_id', '=', isset($where['t.person_id'][1]) ? $where['t.person_id'][1] : 0)->field('person_id, per_name')->find();
		//可选店员
		$where = $this->person['manager'] ? ['store_id' => $this->person['store_id']] : ['person_id' => ['=', $this->person['person_id']]];
		$option_persons = Db::name('store_person')->where($where)->field('person_id, per_name')->select();
		//订单查询起始年份
		$start_year = Db::name('trade')->where('merchant_id', '=', $this->merchant['merchant_id'])->order('trade_id ASC')->value('FROM_UNIXTIME(time_create,"%Y")');
		if(request()->isPost()){
			return \make_json(1, 'ok', $data);
		}
		include \befen\view();
	}

	public function detail($trade_id){
		$value = Db::name('trade')
		->alias('t')
		->join('store_person sp','sp.person_id = t.person_id', 'LEFT')
		->join('store s', 's.store_id = sp.store_id', 'LEFT')
		->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
		->field('t.*, s.store_name, m.merchant_name')
		->where('trade_id', '=', $trade_id)
		->find();
		$value['trade_status'] = model('Trade')::getStatus($value['trade_status']);
		include \befen\view();
	}
}

