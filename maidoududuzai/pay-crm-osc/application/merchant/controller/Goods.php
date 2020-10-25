<?php

namespace app\merchant\controller;

use \think\Db;

class Goods
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
		$where = [
			'g.merchant_id' => $this->merchant['merchant_id'],
			// 'g.store_id' => $this->defaultStoreId,
			'g.is_delete' => 0
		];
		if(input('param.cat_id/d')){
			$where['g.cat_id'] = input('param.cat_id/d');
		}
		$status = input('param.status');
		if($status !== null && $status !== '') {
			$where['g.status'] = $status;
		}
		if(input('param.wd/s')){
			$where['g.goods_no|g.goods_name'] = ['like', '%' . input('param.wd/s') . '%'];
		}
		$object = Db::name('goods')
			->alias('g')
			->join('goods_cat gc', 'g.cat_id = gc.cat_id', 'left')
			->where($where)
			->field('g.*, gc.cat_name')
			->order('sort asc,goods_id desc')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		$goodsCat = Db::name('goods_cat')->where([
			'store_id' => $this->defaultStoreId,
			'is_delete' => 0
		])->field('cat_id,cat_name')->order('sort asc')->select();
		include \befen\view();
	}

	public function add(){
		if( request()->isPost() ){
			$data = input('post.');
			$data['merchant_id'] = $this->merchant['merchant_id'];
			$data['store_id'] = $this->defaultStoreId;
			$goods_name = input('post.goods_name/s');
			if(empty($goods_name)){
				return make_json(0, '商品名必填');
			}
			$cover_pic = input('post.cover_pic/s');
			if(empty($cover_pic)){
				return make_json(0, '商品封面图必填');
			}
			$goods_pic = input('post.goods_pic/a', []);
			if(empty($goods_pic)){
				return make_json(0, '商品主图必填');
			}
			$price = input('post.price/s');
			if(empty($price)){
				return make_json(0, '售价必填');
			}
			$price_original = input('post.price_original/s');
			if(empty($price_original)){
				return make_json(0, '原价必填');
			}
			$goods_no = input('post.goods_no/s');
			if($goods_no && 0 != Db::name('goods')->where('is_delete', '=', 0)->where('goods_no', '=', $goods_no)->count()) {
				return make_json(0, '商品货号已存在');
			}
			$goods_stock = input('post.goods_stock/d', 0);
			$use_attr = input('post.use_attr/d', 0);
			$attr = input('post.attr/a', []);
			if($use_attr && empty($attr)){
				return make_json(0, '规格必填');
			}
			$content = input('post.content/s');
			if(empty($content)){
				return make_json(0, '图文详情必填');
			}
			//todo:防重
			$data['goods_pic'] = JSON(input('post.goods_pic/a', []));
			$data['attr'] = JSON(input('post.attr/a', []));
			model('goods')->allowField(true)->save($data);
			return make_json(1, '操作成功');
		}
		$goodsCat = Db::name('goods_cat')->where([
			'store_id' => $this->defaultStoreId,
			'is_delete' => 0
		])->field('cat_id,cat_name')->order('sort asc')->select();
		include \befen\view('Goods_detail');
	}

	public function detail($goods_id){
		if(request()->isPost()) {
			$goods_no = input('post.goods_no/s');
			if($goods_no && 0 != Db::name('goods')->where('is_delete', '=', 0)->where('goods_no', '=', $goods_no)->where('goods_id', '<>', $goods_id)->count()) {
				return make_json(0, '商品货号已存在');
			}
			$data = input('post.');
			$data['merchant_id'] = $this->merchant['merchant_id'];
			$data['store_id'] = $this->defaultStoreId;
			$data['goods_pic'] = JSON(input('post.goods_pic/a', []));
			$data['attr'] = JSON(input('post.attr/a', []));
			$data['time_update'] = _time();
			$data['use_attr'] = input('post.use_attr/d', 0);
			$data['is_weigh'] = input('post.is_weigh/d', 0);
			model('goods')->allowField(true)->save($data, ['goods_id' => $goods_id]);
			return make_json(1, '操作成功');
		}
		$goodsCat = Db::name('goods_cat')->where([
			'store_id' => $this->defaultStoreId,
			'is_delete' => 0
		])->field('cat_id,cat_name')->order('sort asc')->select();
		$value = Db::name('goods')->where('goods_id', '=', $goods_id)->find();
		$value['goods_pic'] = json_decode($value['goods_pic'], true);
		if(empty($value)){
			return abort(404,'商品不存在');
		}
		include \befen\view('Goods_detail');
	}

	public function delete($goods_id){
		Db::name('goods')->where('goods_id', '=', $goods_id)->update(['is_delete' => 1]);
		return make_json(1, '操作成功');
	}

	public function upload()
	{
		return \app\common\Upload::index();
	}

	public function toggleStatus($goods_id){
		$status = Db::name('goods')->where('goods_id', '=', $goods_id)->value('status');
		$result = Db::name('goods')->where('goods_id', '=', $goods_id)->update(['status' => !$status]);
		if( $result === false ){
			return make_json(0, '操作失败');
		}
		return make_json(1, '操作成功');
	}

}

