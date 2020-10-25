<?php
namespace app\admin\controller\config;
use app\admin\controller\BaseController;

class DeliveryController extends BaseController
{

	//快递列表
	public function index(){
		if (request()->isPost()){
			$data = input('post.');
			if(input('post.id')){
				$result = model('Delivery')->update($data);
			}else{
				$result = model('Delivery')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$deliverylist = model('Delivery')->select()->toArray();

			cookie("prevUrl", request()->url());

			$this->assign('deliverylist', $deliverylist);
			return view();
		}
	}

	//新增修改快递
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			if(input('post.id')){
				$result = model('Delivery')->update($data);
			}else{
				$result = model('Delivery')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$delivery = model('Delivery')->find($id);
				$this->assign('delivery', $delivery);
			}
			return view();
		}
	}

	//改变快递状态
	public function update(){
		$data = input('param.');
		$result = model('Delivery')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}




}