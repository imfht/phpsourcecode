<?php

namespace app\console\controller;

use \think\Db;
use \app\common\Pay;

class SubpaySuixing
{

	public $admin;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
	}

	public function index()
	{
		$Suixing = new \app\common\subpay\Suixing();
		$suixing_orgId = model('Config')->config('suixing_orgId');
		$suixing_PublicKey_body = !file_exists($Suixing->suixing_PublicKey) ? '' : trim(file_get_contents($Suixing->suixing_PublicKey));
		$suixing_PrivateKey_body = !file_exists($Suixing->suixing_PrivateKey) ? '' : trim(file_get_contents($Suixing->suixing_PrivateKey));
		if(request()->isPost()) {
			$post = input('post.');
			$mno = Db::name('gates')->where('sub_gate', '=', 'suixing')->where('merchant_id', '=', $post['merchant_id'])->value('sub_mch_no');
			if(empty($mno)) {
				return make_json(0, '商户编号不存在');
			}
			if($suixing_orgId != $post['suixing_orgId']) {
				Db::name('config')->where('key', '=', 'suixing_orgId')->setField('value', $post['suixing_orgId']);
			}
			if($suixing_PublicKey_body != $post['suixing_PublicKey_body']) {
				file_put_contents($Suixing->suixing_PublicKey, $post['suixing_PublicKey_body']);
				$data['suixing_PublicKey_body'] = $post['suixing_PublicKey_body'];
			}
			if($suixing_PrivateKey_body != $post['suixing_PrivateKey_body']) {
				file_put_contents($Suixing->suixing_PrivateKey, $post['suixing_PrivateKey_body']);
				$data['suixing_PrivateKey_body'] = $post['suixing_PrivateKey_body'];
			}
			model('Config')->list(null, true);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function merchant()
	{
		$where = [];
		$where['g.sub_gate'] = ['=', 'suixing'];
		if(input('param.wd')) {
			$where['m.merchant_no|m.merchant_name'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		$object = Db::name('gates')
			->alias('g')
			->join('merchant m', 'm.merchant_id = g.merchant_id')
			->where($where)
			->order('m.merchant_id', 'DESC')
			->field('g.id, g.sub_mch_no, m.merchant_id, m.merchant_name')
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

	public function merchant_add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$post['sub_gate'] = 'suixing';
			if(empty($post['merchant_id'])) {
				return make_json(0, '请选择商户');
			}
			if(empty($post['sub_mch_no'])) {
				return make_json(0, '请输入商户编号');
			}
			if(0 == number($post['trade_rates'])) {
				return make_json(0, '请输入交易费率');
			}
			if(0 != Db::name('gates')->where('sub_gate', '=', 'suixing')->where('merchant_id', '=', $post['merchant_id'])->count()) {
				return make_json(0, '商户已经存在');
			}
			$post['time_create'] = _time();
			model('Gates')->allowField(true)->save($post);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function merchant_detail($id = 0)
	{
		$value = Db::name('gates')
			->alias('g')
			->join('merchant m', 'm.merchant_id = g.merchant_id')
			->where('g.id', '=', $id)
			->order('m.merchant_id', 'DESC')
			->field('g.id, g.sub_mch_no, g.trade_rates, m.merchant_id, m.merchant_name')
			->find();
		if(request()->isPost()) {
			$post = input('post.');
			$post['sub_gate'] = 'suixing';
			if(empty($post['merchant_id'])) {
				return make_json(0, '请选择商户');
			}
			if(empty($post['sub_mch_no'])) {
				return make_json(0, '请输入商户编号');
			}
			if(0 == number($post['trade_rates'])) {
				return make_json(0, '请输入交易费率');
			}
			if(0 != Db::name('gates')->where('sub_gate', '=', 'suixing')->where('merchant_id', '=', $post['merchant_id'])->where('id', '<>', $id)->count()) {
				return make_json(0, '商户已经存在');
			}
			$post['time_update'] = _time();
			model('Gates')->allowField(true)->save($post, ['id' => $id]);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

}

