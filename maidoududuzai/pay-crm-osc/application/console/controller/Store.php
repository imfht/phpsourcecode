<?php

namespace app\console\controller;

use \think\Db;

class Store
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
			$where['m.merchant_id'] = ['=', input('param.merchant_id')];
		} else {
			if(input('param.merchant_name')) {
				$where['m.merchant_name'] = ['LIKE', '%'.input('param.merchant_name').'%'];
			}
		}
		$object = Db::name('store')
			->alias('s')
			->join('agent a', 'a.agent_id = s.agent_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = s.merchant_id', 'LEFT')
			->where($where)
			->order('store_id', 'DESC')
			->field('s.*, m.merchant_name')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		if(request()->isPost()) {
			$data = [
				'list' => $list,
				'total' => $total,
			];
			return make_json(1, 'ok', $data);
		}
		include \befen\view();
	}

	public function add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			$post['agent_id'] = Db::name('merchant')->where('merchant_id', '=', $post['merchant_id'])->value('agent_id');
			model('Store')->allowField(true)->save($post);
			$store_id = model('Store')->getLastInsID();
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@store', ['store_id' => $store_id]);
			/* HjSync */
			return make_json(1, '添加门店成功');
		}
		include \befen\view();
	}

	public function detail($store_id)
	{
		$value = Db::name('store')
			->alias('s')
			->join('merchant m', 'm.merchant_id = s.merchant_id', 'LEFT')
			->where('s.store_id', '=', $store_id)
			->field('s.*, m.merchant_name')
			->find();
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			model('Store')->allowField(true)->save($post, ['store_id' => $store_id]);
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@store', ['store_id' => $store_id]);
			/* HjSync */
			return make_json(1, '编辑门店成功');
		}
		include \befen\view();
	}
}

