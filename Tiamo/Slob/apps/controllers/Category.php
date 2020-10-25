<?php
namespace App\Controller;

use App\BasicController;
use Swoole;

class Category extends BasicController {

	function index() {
		$numPerPage=  getRequest('numPerPage',20,true);
		$pageNum=  getRequest('pageNum',1,true);
		$category=  model('Category');
		$params= [
			'order'=>'cate_id',
			'limit'=>($pageNum-1)*$numPerPage.','.$numPerPage
		];	
		$total=$category->count(['where'=>1]);
		$page=[
			'numPerPage'=>$numPerPage,
			'pageNum'=>$pageNum,
			'total'=>$total,
		];

		$data=$category->gets($params);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->display('category/index.php');
		$this->display("category/index.php");
	}

	function addCategory() {
		if(isPost()){
			$category=  model('Category');
			$data=$category->getData();
			if($category->create($data)){
				jsonReturn($this->ajaxFromReturn('添加成功',200,'closeCurrent','','category'));
			}else{
				jsonReturn($this->ajaxFromReturn('添加失败',300));
			}
		}
		$this->assign('title', '添加');
		$this->display("category/add_category.php");
	}

	function updateCategory() {
		$category=  model('Category');
		if(isPost()){
			$data=$category->getData();
			if($category->set($data['cate_id'],$data)){
				jsonReturn($this->ajaxFromReturn('修改成功',200,'closeCurrent','','category'));
			}else{
				jsonReturn($this->ajaxFromReturn('修改失败',300));
			}
		}
		$cate_id=  getRequest('cate_id');
		$data=$category->get($cate_id);
		$this->assign('data', $data);
		$this->assign('title', '修改');
		$this->display("category/update_category.php");
	}

	function deleteCategory() {
		$id = getRequest('cate_id');
		$category = model('Category');
		if ($category->del($id)) {
			jsonReturn($this->ajaxFromReturn('删除成功',200,'','','category'));
		} else {
			jsonReturn($this->ajaxFromReturn('删除失败', 300));
		}
	}

	function searchCategory() {
		
	}

}	
