<?php

namespace app\merchant\controller;

use \think\Db;
use \app\common\UserAlipay;

class Bill
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		//
	}

	public function charge()
	{
		$where = [];
		$where['mb.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$where['mb.balance_do'] = ['=', '+'];
		$object = Db::name('mch_bill')
			->alias('mb')
			->join('mch_user mu', 'mu.id = mb.uid', 'LEFT')
			->join('trade t', 't.out_trade_no = mb.out_trade_no', 'LEFT')
			->where($where)
			->order('id', 'DESC')
			->field('mb.*, t.total_amount, mu.username, mu.card_no, mu.user_id, mu.biz_card_no, mu.openid, mu.UserCardCode')
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

	public function consume()
	{
		$where = [];
		$where['mb.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$where['mb.balance_do'] = ['=', '-'];
		$object = Db::name('mch_bill')
			->alias('mb')
			->join('mch_user mu', 'mu.id = mb.uid', 'LEFT')
			->join('trade t', 't.out_trade_no = mb.out_trade_no', 'LEFT')
			->where($where)
			->order('id', 'DESC')
			->field('mb.*, t.total_amount, mu.username, mu.card_no, mu.user_id, mu.biz_card_no, mu.openid, mu.UserCardCode')
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

	public function view_charge($id)
	{
		$where = [];
		$where['mb.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$where['mb.id'] = ['=', $id];
		$where['mb.balance_do'] = ['=', '+'];
		$value = Db::name('mch_bill')
			->alias('mb')
			->join('mch_user mu', 'mu.id = mb.uid', 'LEFT')
			->join('trade t', 't.out_trade_no = mb.out_trade_no', 'LEFT')
			->join('merchant m', 'm.merchant_id = mb.merchant_id', 'LEFT')
			->join('store s', 's.store_id = mb.store_id', 'LEFT')
			->join('store_person sp', 'sp.person_id = mb.person_id', 'LEFT')
			->join('store_device sd', 'sd.device_id = mb.device_id', 'LEFT')
			->where($where)
			->field('mb.*, t.total_amount, mu.username, mu.card_no, mu.user_id, mu.biz_card_no, mu.openid, mu.UserCardCode, m.merchant_name, s.store_name, sp.per_name, sd.SN')
			->find();
		include \befen\view();
	}

	public function view_consume($id)
	{
		$where = [];
		$where['mb.merchant_id'] = ['=', $this->merchant['merchant_id']];
		$where['mb.id'] = ['=', $id];
		$where['mb.balance_do'] = ['=', '-'];
		$value = Db::name('mch_bill')
			->alias('mb')
			->join('mch_user mu', 'mu.id = mb.uid', 'LEFT')
			->join('trade t', 't.out_trade_no = mb.out_trade_no', 'LEFT')
			->join('merchant m', 'm.merchant_id = mb.merchant_id', 'LEFT')
			->join('store s', 's.store_id = mb.store_id', 'LEFT')
			->join('store_person sp', 'sp.person_id = mb.person_id', 'LEFT')
			->join('store_device sd', 'sd.device_id = mb.device_id', 'LEFT')
			->where($where)
			->field('mb.*, t.total_amount, mu.username, mu.card_no, mu.user_id, mu.biz_card_no, mu.openid, mu.UserCardCode, m.merchant_name, s.store_name, sp.per_name, sd.SN')
			->find();
		include \befen\view();
	}

}

