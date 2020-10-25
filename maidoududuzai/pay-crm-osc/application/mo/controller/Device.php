<?php

namespace app\mo\controller;

use \think\Db;

class Device
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
			$where['sd.agent_id'] = ['=', $this->agent['agent_id']];
			if(input('param.SN')) {
				$where['sd.SN'] = ['LIKE', '%'.input('param.SN').'%'];
			}
			if(input('param.store_id')) {
				$where['sd.store_id'] = ['=', input('param.store_id')];
			} else {
				if(input('param.store_name')) {
					$where['sd.store_name'] = ['LIKE', '%'.input('param.store_name').'%'];
				}
			}
			$merchant_id = input('param.merchant_id/d');
			if($merchant_id) {
				$where['sd.merchant_id'] = ['=', $merchant_id];
			} else {
				if(input('param.merchant_name')) {
					$where['m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
				}
			}
			if(request()->isAjax()){
				$object = Db::name('store_device')
					->alias('sd')
					->join('store s', 's.store_id = sd.store_id', 'LEFT')
					->join('merchant m', 'm.merchant_id = sd.merchant_id', 'LEFT')
					->where($where)
					->order('device_id', 'DESC')
					->field('sd.*, s.store_name, m.merchant_name')
					->paginate(20, false, ['query' => request()->param()])
					->each(function($item, $key){
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
			$selected_merchant = Db::name('merchant')->where('merchant_id', '=', $merchant_id)->field('merchant_id, merchant_name')->find();
			include \befen\view();
			return;
		}
		include \befen\view('Device_filter');
	}

}

