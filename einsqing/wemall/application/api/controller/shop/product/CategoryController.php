<?php
namespace app\api\controller\shop\product;
use app\api\controller\BaseController;

class CategoryController extends BaseController
{
	//获取商品分类列表
	public function index(){
		$menulist = model('ProductCategory')->with('file')->select()->toArray();

		$tree = list_to_tree($menulist, 'id', 'pid', 'sub', 'rank', 'desc');

		$data['category'] = $tree;
		return json(['data' => $data, 'msg' => '商品分类', 'code' => 1]);
	}

	// 获取商品分类详情
	public function detail(){
		$map = array();

		if(input('param.id') != ''){
            $map['id|pid']  = input('param.id');
        }
		$menulist = model('ProductCategory')->with('file')->where($map)->select()->toArray();
		$tree = list_to_tree($menulist, 'id', 'pid', 'sub');

		$data['category'] = $tree;
		return json(['data' => $data, 'msg' => '商品分类详情', 'code' => 1]);
	}

}