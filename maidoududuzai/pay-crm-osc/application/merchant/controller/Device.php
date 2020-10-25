<?php

namespace app\merchant\controller;

use \think\Db;
use \app\common\model\Account as AccountModel;
use \app\common\model\Merchant as MerchantModel;

class Device
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		$where = [];
		$where['sd.merchant_id'] = ['=', $this->merchant['merchant_id']];
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

