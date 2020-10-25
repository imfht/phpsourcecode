<?php

namespace app\merchant\controller;

use \think\Db;

class Store
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		$where = [];
		$where['s.merchant_id'] = ['=', $this->merchant['merchant_id']];
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
		$object = Db::name('store')
			->alias('s')
			->join('merchant m', 'm.merchant_id = s.merchant_id', 'LEFT')
			->field('s.*, m.merchant_name')
			->where($where)
			->order('store_id', 'ASC')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		if(request()->isPost()) {
			$data = [
				'list' => $list,
				'total' => $total,
			];
			return make_json(1, 'ok', $data);
		}
		$pagenav = $object->render();
		include \befen\view();
	}


	public function add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			$post['status'] = 1;
			$post['merchant_id'] = $this->merchant['merchant_id'];
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
			->where('s.merchant_id', '=', $this->merchant['merchant_id'])
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

