<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class PictureController extends AdminBaseController {
	public function index(){
		//搜索
		$where=$this->_search();

		//分组
		$tab=M('Category')->where("mid=3 AND pid=0")->order('sort asc')->getField('id,title,is_menu',true);
		$this->assign('tab',$tab);

		$cid=I('cid',0,'intval');
		$this->assign('cid',$cid);
		if($cid){
			if($tab[$cid]['is_menu']){
				$cids=M('Category')->where("pid={$cid}")->getField('id',true);
				$where['cid']=array('IN',$cids);
			}else{
				$where['cid']=$cid;
			}
		}

		//分页
		$limit=$this->_page('Picture',$where);

		//批量
		$this->_batch('Picture');

		//数据
		$list=M('Picture')
			->limit($limit)
			->where($where)
			->order('id DESC')
			->select();
		$this->assign('list',$list);

		//cid替换
		$cate=M('Category')->where("mid=3 AND is_menu =0")->order('sort asc')->getField('id,title',true);
		$this->assign('cate',$cate);

		$this->display();
	}

	public function add(){
		if(IS_POST){$this->addPost();exit;}

		$cid=I('get.cid','0');
		$this->assign('cid',$cid);

		$this->getCate();

		$tag=D('Tag')->getSelect();
		$this->assign('tag',$tag);

		$this->display();
	}
	private function addPost(){
		$Model=D('Picture');
		$post=I('post.','','trim');
		$data=$Model->create($post);
		if(!$data) $this->error($Model->getError());

		$id=$Model->add($data);
		if($id) $this->success('添加成功',U('index'));
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST){$this->editPost();exit;}
		$info=M('Picture')->find($id);
		$this->assign('info',$info);
		
		$this->getCate();

		$this->display();
	}
	private function editPost(){
		$Model=D('Picture');
		$post=I('post.','','trim');
		$data=$Model->create($post);
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return)$this->success('修改成功',U('index'));
		else $this->error('修改失败');
	}

	private function getCate(){
		$cate=M('Category')->where('mid=3')->order('sort asc')->getField('id,pid,title,is_menu');
		$this->assign('cate',$cate);
		$cate_tree=\Lib\ArrayTree::listLevel($cate);
		$this->assign('cate_tree',$cate_tree);
	}

	public function del($id){
		$return=M('Picture')->delete($id);
		if($return){
			D('File')->delFile($id,'Picture');
			$this->success('删除成功');
		}
		else $this->error('删除失败');
	}










}