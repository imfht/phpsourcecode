<?php

namespace app\merchant\controller;

use \think\Db;
use \app\common\UserAlipay;

class User
{

	public $merchant;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
	}

	public function index()
	{
		$where = [];
		$where['mu.merchant_id'] = ['=', $this->merchant['merchant_id']];
		if(input('param.wd')) {
			$where['mu.username|mu.phone|mu.card_no'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		$object = Db::name('mch_user')
			->alias('mu')
			->join('merchant m', 'm.merchant_id = mu.merchant_id', 'LEFT')
			->where($where)
			->order('id', 'DESC')
			->field('mu.*, m.merchant_name')
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

	/**
	 * 会员详情
	 * @param mixed $mch_uid
	 */
	public function detail($mch_uid)
	{
		$value = Db::name('mch_user')
			->alias('mu')
			->where('id', '=', $mch_uid)
			->where('merchant_id', '=', $this->merchant['merchant_id'])
			->find();
		include \befen\view();
	}

	/**
	 * 会员调账
	 * @param mixed $mch_uid
	 * @param string $credit 积分 可选
	 * @param string $balance 余额 可选
	 */
	public function update($mch_uid)
	{
		$value = model('MchUser')->get_user(['id' => $mch_uid, 'merchant_id' => $this->merchant['merchant_id']]);
		if(request()->isPost()) {
			$post = input('post.');
			$post['balance_amount'] = $post['balance_amount'] ? $post['balance_amount'] : 0;
			$post['credit_amount'] = $post['credit_amount'] ? $post['credit_amount'] : 0;
			if($post['balance_amount'] || $post['credit_amount']) {
				model('MchUser')->mch_user_update($this->merchant, $value, [
					'balance_do' => $post['balance_do'],
					'balance_amount' => $post['balance_amount'],
					'credit_do' => $post['credit_do'],
					'credit_amount' => $post['credit_amount'],
				]);
			}
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function charge()
	{
		$where = [];
		$where['merchant_id'] = ['=', $this->merchant['merchant_id']];
		$object = Db::name('mch_charge')
			->where($where)
			->order('pay_amount', 'ASC')
			->paginate(100, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		include \befen\view();
	}

	public function charge_add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['merchant_id'] = $this->merchant['merchant_id'];
			$post['time_create'] = _time();
			model('MchCharge')->allowField(true)->save($post);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function charge_mod($id)
	{
		$value = model('MchCharge')->get([
			'id' => $id,
			'merchant_id' => $this->merchant['merchant_id']
		]);
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			model('MchCharge')->allowField(true)->save($post, [
				'id' => $id,
				'merchant_id' => $this->merchant['merchant_id']
			]);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function charge_del($id)
	{
		if(request()->isPost()) {
			model('MchCharge')->where('id', '=', $id)->where('merchant_id', '=', $this->merchant['merchant_id'])->delete();
			return make_json(1, '操作成功');
		}
	}

}

