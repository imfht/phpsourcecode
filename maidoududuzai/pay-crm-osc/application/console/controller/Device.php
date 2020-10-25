<?php

namespace app\console\controller;

use \think\Db;

class Device
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
			->join('agent a', 'a.agent_id = sd.agent_id', 'LEFT')
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

	public function add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_create'] = _time();
			if(Db::name('store_device')->where('SN', '=', $post['SN'])->count()) {
				return make_json(0, 'SN已经存在');
			}
			if(!$post['store_id']) {
				$post['store_id'] = model('Store')->get_one_store($post['merchant_id']);
			}
			$merchant = Db::name('merchant')->where('merchant_id', '=', $post['merchant_id'])->field('agent_id, merchant_id')->find();
			$post['agent_id'] = $merchant['agent_id'];
			$post['merchant_id'] = $merchant['merchant_id'];
			model('StoreDevice')->allowField(true)->save($post);
			return make_json(1, '添加设备成功');
		}
		include \befen\view();
	}

	public function detail($device_id)
	{
		$value = Db::name('store_device')
			->alias('sd')
			->join('store s', 's.store_id = sd.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = sd.merchant_id', 'LEFT')
			->where('device_id', '=', $device_id)
			->field('sd.*, s.store_id, s.store_name, m.agent_id, m.merchant_id, m.merchant_name')
			->find();
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			if(Db::name('store_device')->where('SN', '=', $post['SN'])->where('device_id', '<>', $device_id)->count()) {
				return make_json(0, 'SN已经存在');
			}
			if(!$post['store_id']) {
				$post['store_id'] = model('Store')->get_one_store($post['merchant_id']);
			}
			$merchant = Db::name('merchant')->where('merchant_id', '=', $post['merchant_id'])->field('agent_id, merchant_id')->find();
			$post['agent_id'] = $merchant['agent_id'];
			$post['merchant_id'] = $merchant['merchant_id'];
			model('StoreDevice')->allowField(true)->save($post, ['device_id' => $device_id]);
			return make_json(1, '编辑设备成功');
		}
		include \befen\view();
	}

	public function update($device_id)
	{
		$value = Db::name('store_device')
			->alias('sd')
			->join('store s', 's.store_id = sd.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = sd.merchant_id', 'LEFT')
			->where('device_id', '=', $device_id)
			->field('sd.*, s.store_id, s.store_name, m.agent_id, m.merchant_id, m.merchant_no')
			->find();
		if(empty($value['SN'])) {
			echo '当前设备不允许改签';
			exit();
		}
		if(request()->isPost()) {
			$post = input('post.');
			if($post['merchant_id'] == $value['merchant_id']) {
				return make_json(0, '当前商户与改签商户一致');
			}
			if(Db::name('store_device')->where('SN', '=', $value['SN'])->where('device_id', '<>', $device_id)->count()) {
				return make_json(0, 'SN已经存在');
			}
			Db::name('store_device')->where('device_id', '=', $device_id)->update([
				'SN' => '',
				'SN_bak' => $value['SN'],
				'status' => 0,
				'time_update' => _time(),
			]);
			if(!$post['store_id']) {
				$post['store_id'] = model('Store')->get_one_store($post['merchant_id']);
			}
			$merchant = Db::name('merchant')->where('merchant_id', '=', $post['merchant_id'])->field('agent_id, merchant_id')->find();
			$post['SN'] = $value['SN'];
			$post['SN_bak'] = '';
			$post['status'] = $value['status'];
			$post['trade_gate'] = $value['trade_gate'];
			if(!Db::name('store_device')->where('SN', '=', '')->where('SN_bak', '=', $value['SN'])->where('merchant_id', '=', $merchant['merchant_id'])->count()) {
				$post['time_create'] = _time();
				$post['agent_id'] = $merchant['agent_id'];
				$post['merchant_id'] = $merchant['merchant_id'];
				model('StoreDevice')->allowField(true)->save($post);
			} else {
				$post['time_update'] = _time();
				model('StoreDevice')->allowField(true)->save($post, [
					'SN' => '',
					'SN_bak' => $value['SN'],
					'merchant_id' => $merchant['merchant_id'],
				]);
			}
			return make_json(1, '设备改签成功');
		}
		include \befen\view();
	}

	public function upload()
	{
		return \app\common\Upload::index(null, true);
	}

	public function slider($device_id)
	{
		$value = Db::name('store_device')->where('device_id', '=', $device_id)->find();
		$noad = '/public/image/noad.png';
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			$post['time'] = intval($post['time']);
			if(!$post['item']) {
				$post['item'] = [];
			} else {
				$post['item'] = explode(',', $post['item']);
				foreach($post['item'] as $key => $val) {
					if($val == $noad) {
						unset($post['item'][$key]);
					}
				}
				$post['item'] = array_values($post['item']);
			}
			$ads = JSON(['type' => $post['type'], 'time' => $post['time'], 'item' => $post['item'], 'video' => $post['video']]);
			Db::name('store_device')->where('device_id', '=', $device_id)->update(['ads' => $ads]);
			return make_json(1, '操作成功');
		}
		$list = [];
		$ads = json_decode($value['ads'], true);
		if(empty($ads['type']) || !in_array($ads['type'], ['image', 'video'])) {
			$ads['type'] = 'image';
		}
		if(empty($ads['time'])) {
			$ads['time'] = 5;
		}
		if(empty($ads['video'])) {
			$ads['video'] = '';
		}
		if(empty($ads['item'])) {
			$list = [];
		} else {
			$list = $ads['item'];
		}
		include \befen\view();
	}

}

