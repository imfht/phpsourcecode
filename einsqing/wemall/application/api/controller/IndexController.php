<?php
namespace app\api\controller;

class IndexController extends BaseController
{
	public function index()
    {
    	$user_id = $this->get_user_id();
    	dump($user_id);

    }

















    public function ceshi()
    {
        $params['order_id'] = 1;
        \think\Hook::exec('app\\common\\behavior\\AppletTplMsg','ceshi',$params);
    }

    public function ceshi2()
    {
    	// (new \app\admin\controller\publiccontroller)->ceshi();//跨控制器调用
    	action('Admin/PublicController/ceshi',['id' => 5,'ids' => 6]);
    }
}
