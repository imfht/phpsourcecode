<?php

namespace app\console\controller;

use \think\Db;

class Admin
{

	public $admin;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
	}

	public function index()
	{
		$where = [];
		if(input('param.wd')) {
			$where['username|pname'] = ['LIKE', '%'.input('param.wd').'%'];
		}
		$object = Db::name('admin')
			->where($where)
			->order('id', 'ASC')
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
			if(model('Admin')->where('username', '=', input('post.username'))->count()) {
				return make_json(0, '账号已经存在');
			}
			$post = input('post.');
			$post['status'] = 1;
			$post['password'] = authcode($post['password'], 'ENCODE');
			model('Admin')->allowField(true)->save($post);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function mod($id)
	{
		$value = model('Admin')->get_one($id);
		if(request()->isPost()) {
			$post = input('post.');
			if(!$post['password']) {
				unset($post['password']);
			} else {
				$post['password'] = authcode($post['password'], 'ENCODE');
			}
			model('Admin')->allowField(true)->save($post, ['id' => $id]);
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function del($id)
	{
		if(request()->isPost()) {
			$value = model('Admin')->get_one($id);
			if(1 == $value['id'] || 'admin' == $value['username']) {
				return make_json(0, '禁止操作');
			}
			model('Admin')->destroy($id);
			return make_json(1, '操作成功');
		}
	}

	public function status()
	{
		if(request()->isPost()) {
			$id = input('post.id');
			$value = model('Admin')->get_one($id);
			if(1 == $value['id'] || 'admin' == $value['username']) {
				return make_json(0, '禁止操作');
			}
			if($value['status'] == 0) {
				model('Admin')->allowField(true)->save(['status' => 1], ['id' => $id]);
				return make_json(1, '操作成功');
			} else {
				model('Admin')->allowField(true)->save(['status' => 0], ['id' => $id]);
				return make_json(1, '操作成功');
			}
		}
	}

}

