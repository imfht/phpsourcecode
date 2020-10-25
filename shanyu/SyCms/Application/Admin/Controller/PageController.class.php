<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class PageController extends AdminBaseController {

	public function index(){

		//分页
		$limit=$this->_page('page',$where);

		//数据
		$list=M('page a')
			->join('__CATEGORY__ c ON a.cid=c.id')
			->field('a.*,c.title as cname')
			->limit($limit)
			->where($where)
			->select();
		$this->assign('list',$list);

		$this->display();
	}

	public function add(){
		if(IS_POST){$this->addPost();exit;}

		$cid=I('get.cid','0');
		$this->assign('cid',$cid);

		$this->getCate();

		$this->display();
	}
	private function addPost(){
		$Model=D('page');
		$post=I('post.','','trim');
		$data=$Model->create($post);
		if(!$data) $this->error($Model->getError());

		$id=$Model->add($data);
		if($id)$this->success('添加成功',U('index'));
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost();exit;}
		$info=M('page')->find($id);
		$this->assign('info',$info);
		
		$this->getCate();

		$this->display();
	}
	private function editPost(){
		$Model=D('page');
		$post=I('post.','','trim');
		$data=$Model->create($post);
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功',U('index'));
		else $this->error('修改失败');
	}

	private function getCate(){
		$cate=M('Category')->where("mid=2")->getField('id,title');
		$this->assign('cate',$cate);
	}

	public function del($id){
		$return=M('page')->delete($id);
		if($return)$this->success('删除成功');
		else $this->error('删除失败');
	}










}