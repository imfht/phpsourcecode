<?php

namespace app\merchant\controller;

use \think\Db;
use \app\common\Pay;

class Order
{
	public $merchant;
	public $defaultStoreId;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
		$this->defaultStoreId = model('Store')->get_one_store($this->merchant['merchant_id']);
	}

	public function index(String $out_trade_no = '', String $time_create = '', String $is_pay = null)
	{
		$where = [
			'o.merchant_id' => $this->merchant['merchant_id']
		];
		if($is_pay !== null){
			if($is_pay){
				$where['o.status'] = ['>', 0];
			}else{
				$where['o.status'] = ['=', 0];
			}
		}
		if($out_trade_no){
			$where['out_trade_no'] = ['like', "%$out_trade_no%"];
		}
		if($time_create) {
			$time_create_range = explode('~', $time_create);
			$time_create_range = array_map(function($v){
				return gstime(trim($v));
			}, $time_create_range);
			$time_create_range[1] += 86400;
			$where['o.time_create'] = ['BETWEEN TIME', $time_create_range];
		}
		$object = Db::name('order')
		->alias('o')
		->where($where)
		->field('o.*')
		->order('order_id desc')
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

	public function detail($order_id){
		//更新交易状态
		try {
			$out_trade_no = Db::name('order')->where('order_id', '=', $order_id)->value('out_trade_no');
			\app\pay\controller\Index::query(Pay::merchant($this->merchant['merchant_id']), $out_trade_no);
		} catch (\Throwable $th) {}
		$value = Db::name('order')
		->alias('o')
		->join('store s', 'o.store_id = s.store_id', 'left')
		->join('store_person sp', 'o.person_id = sp.person_id', 'left')
		->join('store_device sd', 'o.device_id = sd.device_id', 'left')
		->where('order_id', '=', $order_id)
		->field('o.*, s.store_name, sp.per_name, sd.SN')
		->find();
		$goods = Db::name('order_detail')
		->alias('od')
		->join('goods g', 'od.goods_id = g.goods_id')
		->where('od.order_id', '=', $order_id)
		->field('od.*, g.goods_name')
		->select();
		foreach ($goods as $key => &$g) {
			$g['attr'] = implode(' / ', json_decode($g['attr'], true));
			$g['attr'] = empty($g['attr']) ? '-' : $g['attr'];
		}
		unset($g);
		$trade = Db::name('trade')
		->where('out_trade_no', '=', $value['out_trade_no'])
		->find();
		$mch_user_id = Db::name('mch_bill')->where('out_trade_no', '=', $value['out_trade_no'])->value('uid');
		if($mch_user_id){
			$mch_user = Db::name('mch_user')->where('id', '=', $mch_user_id)->find();
		}
		include \befen\view();
	}
}

