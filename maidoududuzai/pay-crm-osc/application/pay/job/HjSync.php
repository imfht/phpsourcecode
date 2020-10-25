<?php

namespace app\pay\job;

use \think\Db;
use \think\queue\Job;

class HjSync
{

	private $v = '8TCk8HnMPDqRCnbo';

	private $hj_api = 'https://pay.tryyun.net/hj/web/index.php?r=api';

	public function fire(Job $job, $data)
	{
		
	}

	public function test($data)
	{
		return http()->get_error($data);
	}

	public function merchant(Job $job, $data)
	{

		$merchant = Db::name('merchant')->where('merchant_id', '=', $data['merchant_id'])->field('merchant_no, merchant_name, password, per_name, per_phone, per_email')->find();
		$data = [];
		$data['v'] = $this->v;
		foreach($merchant as $key => $val) {
			if($key != 'password') {
				$data[$key] = $val;
			} else {
				$data[$key] = authcode($val, 'DECODE');
			}
		}
		echo Tool::show($data);
		$res = http()->post("{$this->hj_api}/merchant/index", 30, $data);
		$res = json_decode($res, true);
		echo Tool::show($res);
		if(isset($res['status']) || http()->get_error($res)) {
			$job->delete();
		}

	}

	public function store(Job $job, $data)
	{

		$store = Db::name('store')
			->alias('s')
			->join('merchant m', 'm.merchant_id = s.merchant_id')
			->where('store_id', '=', $data['store_id'])
			->field('s.store_id, s.store_name, s.per_phone, m.merchant_no')
			->find();
		$data = [];
		$data['v'] = $this->v;
		foreach($store as $key => $val) {
			$data[$key] = $val;
		}
		$data = array_key_replace($data, ['store_id' => 'out_shop_id', 'store_name' => 'name', 'per_phone' => 'mobile']);
		echo Tool::show($data);
		$res = http()->post("{$this->hj_api}/store/index", 30, $data);
		$res = json_decode($res, true);
		echo Tool::show($res);
		if(isset($res['status']) || http()->get_error($res)) {
			$job->delete();
		}

	}

	public function store_person(Job $job, $data)
	{

		$store_person = Db::name('store_person')
			->alias('sp')
			->join('store s', 's.merchant_id = sp.merchant_id')
			->join('merchant m', 'm.merchant_id = sp.merchant_id')
			->where('person_id', '=', $data['person_id'])
			->field('sp.person_id, sp.per_name, sp.per_phone, sp.password, s.store_id, m.merchant_no')
			->find();
		$data = [];
		$data['v'] = $this->v;
		foreach($store_person as $key => $val) {
			if($key != 'password') {
				$data[$key] = $val;
			} else {
				$data[$key] = authcode($val, 'DECODE');
			}
		}
		$data = array_key_replace($data, ['store_id' => 'out_shop_id', 'per_name' => 'per_name', 'per_phone' => 'per_phone']);
		echo Tool::show($data);
		$res = http()->post("{$this->hj_api}/person/index", 30, $data);
		$res = json_decode($res, true);
		echo Tool::show($res);
		if(isset($res['status']) || http()->get_error($res)) {
			$job->delete();
		}

	}

}

