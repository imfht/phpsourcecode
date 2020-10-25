<?php
namespace app\admin\controller\user;
use app\admin\controller\BaseController;

class RechargeController extends BaseController
{
	//充值列表
	public function index(){
		$rechargelist = model('Trade')->with('user')->where('type',1)->order('id desc')->paginate();
		// halt($rechargelist->toArray());

		cookie("prevUrl", $this->request->url());

		$this->assign('rechargelist', $rechargelist);
		return view();
	}

}