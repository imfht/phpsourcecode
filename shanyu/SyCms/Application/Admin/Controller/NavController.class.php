<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class NavController extends AdminBaseController {

	public function index(){
		//分组
		$groups=I('groups',1,'intval');
		$this->assign('groups',$groups);
		$where['groups']=$groups;

		//分页
		$limit=$this->_page('Nav',$where);

		//批量
		$this->_batch('Nav');

		//数据
		$list=M('Nav')
			->limit($limit)
			->where($where)
			->select();
		$this->assign('list',$list);

		$this->display();
	}
	public function add(){
		if(IS_POST){$this->addPost();exit;}
		$this->display();
	}
	private function addPost(){
		$Model=D('Nav');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->add($data);
		if($return) $this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost();exit;}
		$info=M('Nav')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	private function editPost(){
		$Model=D('Nav');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function del($id){
		$return=M('Nav')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}




}