<?php

namespace app\agent\controller;

use \think\Db;

class Person
{

	public $agent;

	public function __construct()
	{
		$this->agent = model('Agent')->checkLoginAgent();
	}

	public function index()
	{
		$where = [];
		$where['sp.agent_id'] = ['=', $this->agent['agent_id']];
		if(input('param.wd')) {
			$where['sp.per_name|sp.per_phone'] = ['LIKE', '%'.input('param.wd').'%'];
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
		$object = Db::name('store_person')
			->alias('sp')
			->join('store s', 's.store_id = sp.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = sp.merchant_id', 'LEFT')
			->join('wx_user wx', 'sp.openid = wx.openid', 'LEFT')
			->where($where)
			->order('person_id', 'ASC')
			->field('sp.*, m.merchant_name, s.store_name, wx.nickname, wx.headimgurl')
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
			if(Db::name('store_person')->where('per_phone', '=', $post['per_phone'])->count()) {
				return make_json(0, '员工已经存在');
			}
			if(!$post['store_id']) {
				$post['store_id'] = model('Store')->get_one_store($this->merchant['merchant_id']);
			}
			$password = get_rand(12);
			$post['password'] = authcode($password, 'ENCODE');
			$post['agent_id'] = $this->merchant['agent_id'];
			$post['merchant_id'] = $this->merchant['merchant_id'];
			model('StorePerson')->allowField(true)->save($post);
			$person_id = model('StorePerson')->getLastInsID();
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@store_person', ['person_id' => $person_id]);
			/* HjSync */
			return make_json(1, '添加员工成功');
		}
		include \befen\view();
	}

	public function detail($person_id)
	{
		$value = Db::name('store_person')
			->alias('sp')
			->join('store s', 's.store_id = sp.store_id', 'LEFT')
			->where('sp.person_id', '=', $person_id)
			->where('sp.merchant_id', '=', $this->merchant['merchant_id'])
			->field('sp.*, s.store_name')
			->find();
		if(request()->isPost()) {
			$post = input('post.');
			$post['time_update'] = _time();
			if(Db::name('store_person')->where('per_phone', '=', $post['per_phone'])->where('person_id', '<>', $person_id)->count()) {
				return make_json(0, '员工已经存在');
			}
			if(!$post['store_id']) {
				$post['store_id'] = model('Store')->get_one_store($this->merchant['merchant_id']);
			}
			$post['time_update'] = _time();
			$post['agent_id'] = $this->merchant['agent_id'];
			$post['merchant_id'] = $this->merchant['merchant_id'];
			model('StorePerson')->allowField(true)->save($post, ['person_id' => $person_id, 'merchant_id' => $this->merchant['merchant_id']]);
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@store_person', ['person_id' => $person_id]);
			/* HjSync */
			return make_json(1, '编辑员工成功');
		}
		include \befen\view();
	}

	public function passwd($person_id)
	{
		if(request()->isPost()) {
			$post = [];
			$post['password'] = authcode(input('post.password'), 'ENCODE');
			model('StorePerson')->allowField(true)->save($post, ['person_id' => $person_id, 'merchant_id' => $this->merchant['merchant_id']]);
			/* HjSync */
			//class_exists('\app\pay\job\HjSync') && \think\Queue::push('\app\pay\job\HjSync@store_person', ['person_id' => $person_id]);
			/* HjSync */
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function bind_wechat($person_id)
	{
		if(request()->isPost()) {
			echo url('/mp/bind/wechat', ['person_id' => authcode($person_id, 'ENCODE', '', 300)], null, true);
		}
	}

	public function unbind_wechat($person_id)
	{
		if(request()->isPost()) {
			model('StorePerson')->allowField(true)->save(['openid' => ''], ['person_id' => $person_id]);
			return make_json(1, '操作成功');
		}
	}

}

