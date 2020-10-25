<?php
namespace app\admin\controller\shop\product;
use app\admin\controller\BaseController;

class LabelController extends BaseController
{
	//标签列表
	public function index(){
		$labellist = model('ProductLabel')->all();

		cookie("prevUrl", request()->url());
		$this->assign('labellist', $labellist);
		return view();
	}

	//新增修改标签
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			if(input('post.id')){
				$result = model('ProductLabel')->update($data);
			}else{
				$result = model('ProductLabel')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$label = model('ProductLabel')->find($id);
				$this->assign('label', $label);
			}
			return view();
		}
	}

	//删除标签
	public function del(){
		$ids = input('param.id');
		
		$result = model('ProductLabel')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}


}