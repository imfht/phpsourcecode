<?php

namespace app\merchant\controller;

use \think\Db;

class GoodsCat
{

	public $merchant;
	public $defaultStoreId;

	public function __construct()
	{
		$this->merchant = model('Merchant')->checkLoginMerchant();
		$this->defaultStoreId = 0;
		//$this->defaultStoreId = model('Store')->get_one_store($this->merchant['merchant_id']);
	}

	public function index()
	{
		$list = Db::name('goods_cat')->where([
			'merchant_id' => $this->merchant['merchant_id'],
			// 'store_id' => $this->defaultStoreId,
			'is_delete' => 0,
		])->order('sort asc')->select();
		include \befen\view();
	}

	public function add()
	{
		if(request()->isPost()) {
			$post = input('post.');
			$cat_name = input('post.cat_name/s');
			if(empty($cat_name)){
				return make_json(0, '分类名称必填');
			}
			if(Db::name('goods_cat')->where(['store_id' => $this->defaultStoreId, 'cat_name' => $cat_name])->count()) {
				return make_json(0, '分类名称已存在');
			}
			$sort = input('post.sort/d', 99);
			$is_show = input('post.is_show/d', 0);
			$data = [
				'merchant_id' => $this->merchant['merchant_id'],
				'store_id' => $this->defaultStoreId,
				'pid' => 0,
				'cat_name' => $cat_name,
				'sort' => $sort,
				'is_show' => $is_show,
				'time_create' => _time()
			];
			$cat_id = Db::name('goods_cat')->insertGetId($data);
			if(empty($cat_id)) {
				return make_json(0, '新增分类失败');
			}
			return make_json(1, '操作成功');
		}
		include \befen\view();
	}

	public function detail($cat_id)
	{
		if(request()->isPost()) {
			$post = input('post.');
			$cat_name = input('post.cat_name/s');
			if(empty($cat_name)){
				return make_json(0, '分类名称必填');
			}
			if(Db::name('goods_cat')->where('cat_id', '<>', $cat_id)->where(['store_id' => $this->defaultStoreId, 'cat_name' => $cat_name])->count()) {
				return make_json(0, '分类名称已存在');
			}
			$sort = input('post.sort/d', 99);
			$is_show = input('post.is_show/d', 0);
			$data = [
				'cat_name' => $cat_name,
				'sort' => $sort,
				'is_show' => $is_show,
				'time_update' => _time()
			];
			Db::name('goods_cat')->where('cat_id', '=', $cat_id)->update($data);
			return make_json(1, '操作成功');
		}
		$value = Db::name('goods_cat')->where('cat_id', '=', $cat_id)->find();
		include \befen\view();
	}

}

