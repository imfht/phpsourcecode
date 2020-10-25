<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class AuthRuleController extends AdminBaseController {

	public function index(){
		$this->_batch('AuthRule');

		//数据
		$list=M('AuthRule')
			->order('sort ASC,pid ASC,id ASC')
			->select();

		$list=\Lib\ArrayTree::listLevel($list);
		$this->assign('list',$list);
		$this->display();
	}

	public function add(){
		if(IS_POST && IS_AJAX){$this->addPost();exit;}

		$parent=D('AuthRule')->getParent();
		$this->assign('parent',$parent);

		$this->display();
	}
	private function addPost(){
		$Model=D('AuthRule');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->add($data);
		if($return) $this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST && IS_AJAX){$this->editPost($id);exit;}
		$info=M('AuthRule')->find($id);
		$this->assign('info',$info);

		$parent=D('AuthRule')->getParent();
		$this->assign('parent',$parent);

		$this->display();
	}
	private function editPost($id){
		$Model=D('AuthRule');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function del($id){
		$child=M('AuthRule')->where("pid={$id}")->count();
		if($child) $this->error("请先删除当前菜单的子菜单");

		$return=D('AuthRule')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}








}