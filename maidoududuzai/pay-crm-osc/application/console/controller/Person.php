<?php

namespace app\console\controller;

use \think\Db;

class Person
{

	public $admin;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
	}

	public function index()
	{
		$where = [];
		if(input('param.agent_id')) {
			$where['a.agent_id'] = ['=', input('param.agent_id')];
		} else {
			if(input('param.agent_name')) {
				$where['a.agent_name'] = ['LIKE', '%'.input('param.agent_name').'%'];
			}
		}
		if(input('param.wd')) {
			$where['sp.per_name|sp.per_phone'] = ['LIKE', '%'.input('param.wd').'%'];
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
		$object = Db::name('store_person')
			->alias('sp')
			->join('agent a', 'a.agent_id = sp.agent_id', 'LEFT')
			->join('store s', 's.store_id = sp.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = sp.merchant_id', 'LEFT')
			->join('wx_user wx', 'sp.openid = wx.openid', 'LEFT')
			->where($where)
			->order('person_id', 'ASC')
			->field('sp.*, m.merchant_name, s.store_name, wx.nickname, wx.headimgurl')
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

