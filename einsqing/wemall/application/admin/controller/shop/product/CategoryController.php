<?php
namespace app\admin\controller\shop\product;
use app\admin\controller\BaseController;

class CategoryController extends BaseController
{
	//菜单列表
	public function index(){
		$menulist = model('ProductCategory')->with('file')->select()->toArray();

		cookie("prevUrl", request()->url());
		$tree = list_to_tree($menulist, 'id', 'pid', 'sub', 'rank', 'desc');

		$this->assign('menulist', $tree);
		return view();
	}

	//新增修改菜单
	public function add(){
		if (request()->isPost()){
			$data = input('post.');
			if(input('post.id')){
				$result = model('ProductCategory')->update($data);
			}else{
				$result = model('ProductCategory')->create($data);
			}

			if($result){
				$this->success("保存成功", cookie("prevUrl"));
			}else{
				$this->error('保存失败', cookie("prevUrl"));
			}
		}else{
			$id = input('param.id');
			if($id){
				$menu = model('ProductCategory')->find($id);
				$this->assign('menu', $menu);
			}
			$menulist = model('ProductCategory')->all()->toArray();
			$tree = list_to_tree($menulist, 'id', 'pid', 'sub');
            $this->assign("menulist", $tree);
			return view();
		}
	}

	//删除菜单
	public function del(){
		$ids = input('param.id');
		
		$result = model('ProductCategory')->destroy($ids);
		if($result){
			$this->success("删除成功", cookie("prevUrl"));
		}else{
			$this->error('删除失败', cookie("prevUrl"));
		}
	}
}