<?php
namespace app\admin\controller\shop\order;
use app\admin\controller\BaseController;

class BacktypeController extends BaseController
{
	//售后类型列表
	public function index(){
		$typelist = model('OrderFeedbackType')->paginate();
		// dump($typelist->toArray());
		cookie("prevUrl", request()->url());

		$this->assign('typelist', $typelist);
		return view();
	}

	//新增修改售后类型
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			if(input('post.id')){
				$result = model('OrderFeedbackType')->update($data);
			}else{
				$result = model('OrderFeedbackType')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$type = model('OrderFeedbackType')->find($id);
				$this->assign('type', $type);
			}
			return view();
		}
	}

	//改变售后类型状态
	public function update(){
		$data = input('param.');
		$result = model('OrderFeedbackType')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

}