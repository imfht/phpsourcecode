<?php
namespace app\admin\controller\user;
use app\admin\controller\BaseController;

class MsgController extends BaseController
{
	//消息列表
	public function index(){
		$msglist = model('UserMsg')->with('user')->order('id desc')->paginate();
		// halt($msglist->toArray());

		cookie("prevUrl", $this->request->url());

		$this->assign('msglist', $msglist);
		return view();
	}

	//新增修改msg
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			if(input('post.id')){
				$result = model('UserMsg')->update($data);
			}else{
				$result = model('UserMsg')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$msg = model('UserMsg')->find($id);
				$this->assign('msg', $msg);
			}
			return view();
		}
	}

	//改变msg状态
	public function update(){
		$data = input('param.');
		$result = model('UserMsg')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
	}

}