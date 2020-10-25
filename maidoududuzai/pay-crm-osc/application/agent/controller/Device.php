<?php

namespace app\agent\controller;

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
		$where = [];
		$where['sd.agent_id'] = ['=', $this->agent['agent_id']];
		if(input('param.SN')) {
			$where['sd.SN'] = ['LIKE', '%'.input('param.SN').'%'];
		}
		if(input('param.store_id')) {
			$where['s.store_id'] = ['=', input('param.store_id')];
		} else {
			if(input('param.store_name')) {
				$where['s.store_name'] = ['LIKE', '%'.input('param.store_name').'%'];
			}
		}
		if(input('param.merchant_id')) {
			$where['m.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
		}
		$object = Db::name('store_device')
			->alias('sd')
			->join('store s', 's.store_id = sd.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = sd.merchant_id', 'LEFT')
			->where($where)
			->order('device_id', 'DESC')
			->field('sd.*, s.store_name, m.merchant_name')
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

