<?php
namespace app\admin\controller\shop\product;
use app\admin\controller\BaseController;

class CommentController extends BaseController
{

	//评论列表
	public function index(){
		$commentlist = model('ProductComment')->with('user,product')->paginate();

		cookie("prevUrl", request()->url());
		// halt($commentlist->toArray());
		$this->assign('commentlist', $commentlist);
		return view();
	}

	//删除评论
	public function del(){
		$ids = input('param.id');
		
		$result = model('ProductComment')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}

	//改变评论状态
	public function update(){
		$data = input('param.');
		$result = model('ProductComment')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}


}