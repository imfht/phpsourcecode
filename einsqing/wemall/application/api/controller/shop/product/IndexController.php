<?php
namespace app\api\controller\shop\product;
use app\api\controller\BaseController;

class IndexController extends BaseController
{
	//获取商品列表
	public function index(){
		$map = array();
		if(input('param.id') != ''){
            $map['id']  = input('param.id');
        }
        if(input('param.keyword') != ''){
            $map['name|subname']  = ['like','%'.input('param.keyword').'%'];
        }
        if(input('param.category_id') != ''){
            $map['category_id']  = input('param.category_id');
        }
        $map['status']  = 1;

        if(input('param.page')){
        	$data['product'] = model('Product')->with('file,category')->where($map)->order('rank', 'desc')->paginate();
        }else{
        	$data['product'] = model('Product')->with('file,category')->where($map)->order('rank', 'desc')->select();
        }

		return json(['data' => $data, 'msg' => '商品列表', 'code' => 1]);
	}

	//获取商品详情
	public function detail(){
		$id = input('param.id');
		$product = model('Product')->with('file,skus')->find($id);

		$data['product'] = $product;
		return json(['data' => $data, 'msg' => '商品详情', 'code' => 1]);
	}



	

}