<?php
namespace app\api\controller\ads;
use app\api\controller\BaseController;

class IndexController extends BaseController
{
	//获取广告列表
	public function index(){
		$map = array();
		if(input('param.position_id') != ''){
            $map['position_id']  = input('param.position_id');
        }
        
        $map['status']  = 1;
		if(input('param.page')){
            $data['ads'] = model('Ads')->with('file,position')->where($map)->order('rank', 'desc')->paginate();
        }else{
            $data['ads'] = model('Ads')->with('file,position')->where($map)->order('rank', 'desc')->select();
        }
		return json(['data' => $data, 'msg' => '广告', 'code' => 1]);
	}
	//获取广告详情
	public function detail(){
		$id = input('param.id');
		$ads = model('Ads')->with('file,position')->find($id);

		$data['ads'] = $ads;
		return json(['data' => $data, 'msg' => '广告详情', 'code' => 1]);
	}


}