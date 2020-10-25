<?php
namespace app\admin\controller\shop\order;
use app\admin\controller\BaseController;

class BacklistController extends BaseController
{
	//售后列表
	public function index(){
		$backlist = model('ProductExchange')->with('type,order.user,product')->paginate();
		// halt($backlist->toArray());
		cookie("prevUrl", request()->url());

		$this->assign('backlist', $backlist);
		return view();
	}

	//更改售后状态
	public function update(){
		$data = input('param.');
		$result = model('ProductExchange')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

}