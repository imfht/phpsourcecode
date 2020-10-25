<?php
namespace app\admin\controller\shop\product;
use app\admin\controller\BaseController;

class SkuController extends BaseController
{

	//sku列表
	public function index(){
		$skulist = model('Sku')->all()->toArray();

		cookie("prevUrl", request()->url());

		$tree = list_to_tree($skulist, 'id', 'pid', 'sub');
		$this->assign('skulist', $tree);
		return view();
	}

	//新增修改sku
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			if(input('post.id')){
				$result = model('Sku')->update($data);
			}else{
				$result = model('Sku')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$sku = model('Sku')->find($id);
				$this->assign('sku', $sku);
			}
			$skulist = model('Sku')->all()->toArray();
			$tree = list_to_tree($skulist, 'id', 'pid', 'sub');
            $this->assign("skulist", $tree);
			return view();
		}
	}

	//删除sku
	public function del(){
		$ids = input('param.id');
		
		$result = model('Sku')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}

	//获取sku列表
	public function getlist(){
		$map = input('post.');

		$skulist = model('Sku')->where($map)->select();
		return json($skulist);
	}






}