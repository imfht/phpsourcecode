<?php

namespace app\merchant\controller;

use \think\Db;

class Qrcode
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		$where = [];
		$where['q.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$object = Db::name('qrcode')
			->alias('q')
			->join('store s', 's.store_id = q.store_id', 'LEFT')
			->join('store_person sp', 'sp.person_id = q.person_id', 'LEFT')
			->join('store_device sd', 'sd.device_id = q.device_id', 'LEFT')
			->where($where)
			->order('id', 'ASC')
			->field('q.*, s.store_name, sp.per_name, sd.SN')
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

	public function create()
	{
		if(request()->isPost()) {
			$count = intval(input('post.count'));
			if($count < 1) {
				return make_json(0, '请输入收款码数量');
			}
			if($count > 10) {
				return make_json(0, '每次最多生成10个收款码');
			}
			$id = Db::name('qrcode')->order('id', 'DESC')->value('id');
			$id = intval($id);
			if($id == 0) {
				$id = pow(10, 6) + 1;
				Db::query("TRUNCATE TABLE `".config('database.prefix')."qrcode`");
				Db::query("ALTER TABLE `".config('database.prefix')."qrcode` Auto_increment={$id}");
			}
			$num = 0;
			$data = [];
			do {
				$data[] = [
					'time_create' => _time(),
					'agent_id' => $this->merchant['agent_id'],
					'merchant_id' => $this->merchant['merchant_id'],
				];
				$num++;
			}
			while($num < $count);
			Db::name('qrcode')->insertAll($data);
			return make_json(1, '操作成功');
		}
	}

	public function detail()
	{
		include \befen\view();
	}

	public function bind_store()
	{
		$id = input('post.id');
		$store_id = input('post.store_id');
		$value = Db::name('store')->where('merchant_id', '=', $this->merchant['merchant_id'])->where('store_id', '=', $store_id)->find();
		if(!$value) {
			return make_json(0, '门店不存在');
		}
		model('Qrcode')->allowField(true)->save(['store_id' => $store_id, 'person_id' => '0', 'device_id' => '0'], ['id' => $id]);
		return make_json(1, '操作成功');
	}

	public function bind_person()
	{
		$id = input('post.id');
		$person_id = input('post.person_id');
		$value = Db::name('store_person')->where('merchant_id', '=', $this->merchant['merchant_id'])->where('person_id', '=', $person_id)->find();
		if(!$value) {
			return make_json(0, '员工不存在');
		}
		$store_id = $value['store_id'];
		$person_id = $value['person_id'];
		if($store_id != Db::name('qrcode')->where('id', '=', $id)->value('store_id')) {
			return make_json(0, '员工与设备必须属于同一门店');
		}
		model('Qrcode')->allowField(true)->save(['store_id' => $store_id, 'person_id' => $person_id], ['id' => $id]);
		return make_json(1, '操作成功');
	}

	public function bind_device()
	{
		$id = input('post.id');
		$SN = input('post.SN');
		$value = Db::name('store_device')->where('merchant_id', '=', $this->merchant['merchant_id'])->where('SN', '=', $SN)->find();
		if(!$value) {
			return make_json(0, '设备不存在');
		}
		$store_id = $value['store_id'];
		$device_id = $value['device_id'];
		if($store_id != Db::name('qrcode')->where('id', '=', $id)->value('store_id')) {
			return make_json(0, '员工与设备必须属于同一门店');
		}
		model('Qrcode')->allowField(true)->save(['store_id' => $store_id, 'device_id' => $device_id], ['id' => $id]);
		return make_json(1, '操作成功');
	}

}

