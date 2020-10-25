<?php

namespace app\mo\controller;

use \think\Db;

class Banks
{

	public $agent;

	public function __construct()
	{
		$this->agent = model('Agent')->checkLoginAgent();
	}
	
	public function index()
	{
		$where = [];
		if(input('param.wd')) {
			$where['bank_name'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		if(input('param.account_bank')) {
			$where['bank_name'] = ['LIKE', '%'.input('param.account_bank').'%'];
		}
		if(input('param.bank_name')) {
			$where['bank_name'] = ['LIKE', '%'.input('param.bank_name').'%'];
		}
		$object = Db::name('banks')
			->where($where)
			->order('bank_code')
			->field('bank_code, bank_name, bank_name as account_bank')
			->paginate(10, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$data = [
			'list' => $list,
			'total' => $total,
		];
		return make_json(1, 'ok', $data);
	}

}

