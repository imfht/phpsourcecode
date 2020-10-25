<?php

namespace app\mo\controller;

use \think\Db;

class Store
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
			$where['s.agent_id'] = ['=', $this->agent['agent_id']];
			if(input('param.wd')) {
				$where['s.store_name'] = ['LIKE', '%'.input('param.wd').'%'];
			}
			if(input('param.store_id')) {
				$where['s.store_id'] = ['=', input('param.store_id')];
			} else {
				if(input('param.store_name')) {
					$where['s.store_name'] = ['LIKE', '%'.input('param.store_name').'%'];
				}
			}
			if(input('param.merchant_id')) {
				$where['s.merchant_id'] = ['=', input('param.merchant_id')];
			} else {
				if(input('param.merchant_name')) {
					$where['m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
				}
			}
	
			if(request()->isAjax()){
				$object = Db::name('store')
					->alias('s')
					->join('agent a', 'a.agent_id = s.agent_id', 'LEFT')
					->join('merchant m', 'm.merchant_id = s.merchant_id', 'LEFT')
					->where($where)
					->order('store_id', 'DESC')
					->field('s.*, m.merchant_name')
					->paginate(20, false, ['query' => request()->param()])
					->each(function($item, $key){
						$item['store_status'] = model('store')::getStatus($item['store_status']);
						return $item;
					});
				$array = $object->toArray();
				$total = $array['total'];
				$list = $array['data'];
				$per_page = $array['per_page'];
				$current_page = $array['current_page'];
				$last_page = $array['last_page'];
				$pagenav = $object->render();
				return \make_json(1, 'ok', [
					'list' => $list,
					'total' => $total,
					'last_page' => $last_page
				]);
			}
			include \befen\view();
			return;
		}
		include \befen\view('Store_filter');
	}

	public function detail($store_id){
		$value = Db::name('store')
		->alias('s')
		->join('merchant m', 'm.merchant_id = s.merchant_id', 'LEFT')
		->field('s.*, m.merchant_name')
		->where('store_id', '=', $store_id)
		->find();
		$value['store_status'] = model('store')::getStatus($value['store_status']);
		include \befen\view();
	}
}

