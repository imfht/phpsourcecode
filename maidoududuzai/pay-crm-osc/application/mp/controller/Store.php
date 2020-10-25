<?php

namespace app\mp\controller;

use \think\Db;

class Store
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
		$where['s.merchant_id'] = $this->person['merchant_id'];
		if(input('param.wd')) {
			$where['s.store_name'] = ['like', '%'.input('param.wd').'%'];
		}
		if(input('param.store_id')) {
			$where['s.store_id'] = ['=', input('param.store_id')];
		} else {
			if(input('param.store_name')) {
				$where['s.store_name'] = ['like', '%'.input('param.store_name').'%'];
			}
		}
		$object = Db::name('store')
			->alias('s')
			->join('merchant m', 'm.merchant_id = s.merchant_id', 'LEFT')
			->field('s.*, m.merchant_name')
			->where($where)
			->order('store_id', 'DESC')
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
		if(request()->isPost()) {
			$data = [
				'list' => $list,
				'total' => $total,
			];
			return make_json('1', 'ok', $data);
		}
		$pagenav = $object->render();
		include \befen\view();
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

