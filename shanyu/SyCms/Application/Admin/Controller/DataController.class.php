<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class DataController extends AdminBaseController {

	public function index(){

		//搜索
		$where = $this->_search();

		//分组
		$groups=I('groups',1,'intval');
		$this->assign('groups',$groups);
		$where['groups']=$groups;

		//分页
		$limit=$this->_page('Data',$where);
		//数据
		$list=M('Data')
			->limit($limit)
			->where($where)
			->order('id DESC')
			->select();
		$this->assign('list',$list);

		$this->display();
	}
	public function add(){
		if(IS_POST){$this->addPost();exit;}
		$type=I('get.type','1');
		$this->assign('type',$type);
		$this->display();
	}
	private function addPost(){
		$Model=D('Data');
		$post=I('post.','','trim');
		if($post['type']==4){
			$post['value']=serialize($post['value']);
		}
		$data=$Model->create($post);
		if(!$data) $this->error($Model->getError());

		$id=$Model->add($data);
		if($id){
			$this->success('添加成功',U('index'));
		}
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost();exit;}
		$info=M('Data')->find($id);
		if($info['type']==4){
			$info['value']=unserialize($info['value']);
		}

		$this->assign('info',$info);
		
		$type=I('get.type','1');
		$this->assign('type',$type);

		$this->display();
	}
	private function editPost(){
		$Model=D('Data');
		$post=I('post.','','trim');
		if($post['type']==4){
			$post['value']=serialize($post['value']);
		}
		$data=$Model->create($post);

		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return){
			$this->success('修改成功',U('index'));
		}
		else $this->error('修改失败');
	}

	public function del($id){
		$return=M('Data')->delete($id);
		if($return){
			$this->success('删除成功');
		}
		else $this->error('删除失败');
	}





}