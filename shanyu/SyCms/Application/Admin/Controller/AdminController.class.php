<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class AdminController extends AdminBaseController {

	public function index(){
		//搜索
		$where=$this->_search();

		//分页
		$limit=$this->_page('Admin',$where);

		//数据
		$list=M('Admin')
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
		$Model=D('Admin');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->add($data);
		if($return) $this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost($id);exit;}
		$info=M('Admin')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	private function editPost($id){
		$Model=D('Admin');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function my($id=UID){
		if(IS_POST){$this->editPost($id);exit;}

		$info=M('Admin')->find($id);
		$this->assign('info',$info);
		$log=M('AdminLog')->where("uid={$id}")->order('log_time asc')->find();
		$this->assign('log',$log);

		$this->display();
	}

	public function group($uid){
		if(IS_POST){$this->groupPost($uid);exit;}
		$this->assign('uid',$uid);

		$group=D('AuthGroup')->getParent(0);
		$this->assign('group',$group);

		$group_id=D('AuthAccess')->where("uid={$uid}")->getField('group_id');
		if(!$group_id) $group_id=0;
		$this->assign('group_id',$group_id);

		$this->display();
	}
	private function groupPost($uid){
		$data=D('AuthAccess')->create();
		if(!$data) $this->error(D('AuthAccess')->getError());

		if(I('post.type')){
			if($data['group_id'] == 0){
				$return=D('AuthAccess')->where("uid={$data['uid']}")->delete();
			}else{
				$return=D('AuthAccess')->where("uid={$data['uid']}")->save($data);
			}
		}else{
			$return=D('AuthAccess')->where("uid={$data['uid']}")->add($data);
		}

		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function del($id){
		$return=M('Admin')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}



}