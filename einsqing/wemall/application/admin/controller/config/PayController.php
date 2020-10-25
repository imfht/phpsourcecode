<?php
namespace app\admin\controller\config;
use app\admin\controller\BaseController;

class PayController extends BaseController
{
	//商城设置
	public function index(){
		if (request()->isPost()){
			$data = input('post.');

			$result = model('Payment')->update($data);
			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$paymentlist = model('Payment')->select()->toArray();

			cookie("prevUrl", request()->url());

			$this->assign('paymentlist', $paymentlist);
			return view();
		}
	}

	//配置支付方式
	public function add(){
		if (request()->isPost()){
			$data = input('post.');

			$result = model('Payment')->update($data);
			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$payment = model('Payment')->find($id);
				// halt($payment->toArray());
				$this->assign('payment', $payment);
			}
			return view();
		}
	}

	//开启关闭支付类型
	public function update(){
		$data = input('param.');
		$result = model('Payment')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

}