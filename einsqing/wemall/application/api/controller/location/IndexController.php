<?php
namespace app\api\controller\location;
use app\api\controller\BaseController;

class IndexController extends BaseController
{
	// 获取地址信息
    public function index()
    {
    	$locationlist = model('Location')->all()->toArray();

    	$tree = list_to_tree($locationlist, 'id', 'pid', 'sub');
    	$data['location'] = $tree;
		return json(['data' => $data, 'msg' => '地址列表', 'code' => 1]);
    }


}