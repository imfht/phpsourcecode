<?php
namespace App\Controller;

use App\BasicController;
use Swoole;

class Admin extends BasicController {

	function index() {
		$numPerPage=  getRequest('numPerPage',20,true);
		$pageNum=  getRequest('pageNum',1,true);
		$admin=  model('admin');
		$params= [
			'order'=>'id',
			'limit'=>($pageNum-1)*$numPerPage.','.$numPerPage
		];	
		$total=$admin->count(['where'=>1]);
		$page=[
			'numPerPage'=>$numPerPage,
			'pageNum'=>$pageNum,
			'total'=>$total,
		];

		$data=$admin->gets($params);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->display('admin/index.php');
		$this->display("admin/index.php");
	}

	function addAdmin() {
		if(isPost()){
			$admin=  model('admin');
			$data=$admin->getData();
			if($admin->create($data)){
				jsonReturn($this->ajaxFromReturn('添加成功',200,'closeCurrent','','admin'));
			}else{
				jsonReturn($this->ajaxFromReturn('添加失败',300));
			}
		}
		$this->assign('title', '添加');
		$this->display("admin/add_admin.php");
	}

	function updateAdmin() {
		$admin=  model('admin');
		if(isPost()){
			$data=$admin->getData();
			if($admin->set($data['id'],$data)){
				jsonReturn($this->ajaxFromReturn('修改成功',200,'closeCurrent','','admin'));
			}else{
				jsonReturn($this->ajaxFromReturn('修改失败',300));
			}
		}
		$id=  getRequest('id');
		$data=$admin->get($id);
		$this->assign('data', $data);
		$this->assign('title', '修改');
		$this->display("admin/update_admin.php");
	}

	function deleteAdmin() {
		$id = getRequest('id');
		$admin = model('admin');
		if ($admin->del($id)) {
			jsonReturn($this->ajaxFromReturn('删除成功',200,'','','admin'));
		} else {
			jsonReturn($this->ajaxFromReturn('删除失败', 300));
		}
	}

	function searchAdmin() {
		
	}

}	
