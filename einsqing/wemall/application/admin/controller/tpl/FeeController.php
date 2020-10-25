<?php
namespace app\admin\controller\tpl;
use app\admin\controller\BaseController;

class FeeController extends BaseController
{
	//费用模版列表
	public function index(){
		$feeList = model('FeeTpl')->order('id desc')->paginate();
		cookie("prevUrl", request()->url());

		$this->assign('feeList', $feeList);
		return view();
	}

	//新增修改
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			$data['status'] = input('?post.status') ? $data['status'] : 0;

			if(input('post.id')){
				$result = model('FeeTpl')->update($data);
			}else{
				$result = model('FeeTpl')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$fee = model('FeeTpl')->find($id);
				$this->assign('fee', $fee);
			}
			return view();
		}
	}

	//改变状态
	public function update(){
		$data = input('param.');
		$result = model('FeeTpl')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

	//删除文章
	public function del(){
		$ids = input('param.id');
		
		$result = model('FeeTpl')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}



}