<?php
namespace app\admin\controller\user;
use app\admin\controller\BaseController;

class TxController extends BaseController
{
	//提现列表
	public function index(){
		$txlist = model('UserTx')->with('user')->order('id desc')->paginate();
		// halt($txlist->toArray());

		cookie("prevUrl", $this->request->url());

		$this->assign('txlist', $txlist);
		return view();
	}

	//改变tx状态
	public function update(){
		$data = input('param.');
		$result = model('UserTx')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

}