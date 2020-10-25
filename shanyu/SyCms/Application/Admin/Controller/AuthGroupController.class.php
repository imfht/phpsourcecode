<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class AuthGroupController extends AdminBaseController {

	public function index(){
		$this->_batch();

		//数据
		$list=M('AuthGroup')
			->where($where)
			->order('pid ASC,id ASC')
			->select();
		$list=\Lib\ArrayTree::listLevel($list);

		$this->assign('list',$list);
		$this->display();
	}

	public function add(){
		if(IS_POST && IS_AJAX){$this->addPost();exit;}

		$parent=D('AuthGroup')->getParent();
		$this->assign('parent',$parent);

		$this->display();
	}
	private function addPost(){
		$data=D('AuthGroup')->create();
		if(!$data) $this->error(D('AuthGroup')->getError());

		$return=D('AuthGroup')->add($data);
		if($return) $this->success('添加成功');
		else $this->error('添加失败');
	}

	public function edit($id){
		if(IS_POST && IS_AJAX){$this->editPost($id);exit;}
		$info=M('AuthGroup')->find($id);
		$this->assign('info',$info);

		$parent=D('AuthGroup')->getParent();
		$this->assign('parent',$parent);

		$this->display();
	}
	private function editPost($id){
		$Model=D('AuthGroup');
		$data=$Model->create();
		if(!$data) $this->error($Model->getError());

		$return=$Model->save($data);
		if($return) $this->success('修改成功');
		else $this->error('修改失败');
	}

	public function rule($id){
		if(IS_POST){$this->rulePost($id);exit;}
		$this->assign('id',$id);
		$rule_all=D('AuthRule')->where("status=1")->getField('id,pid,title,type');
		$user_group=D('AuthGroup')->where("id={$id}")->getField('rules');
		$this->assign('user_group',$user_group);
		$user_rule=explode(',', $user_group);

        $node=array();
        foreach ($rule_all as $v) {
        	$t=array(
        		'id'=>$v['id'],
        		'pId'=>$v['pid'],
        		'name'=>$v['title'],
        	);
        	if($v['pid']==0) $t['open']=true;
        	if(in_array($v['id'], $user_rule)) $t['checked']=true;
        	$node[]=$t;
        }
        $this->assign('node',json_encode($node));

		$this->display();
	}
	private function rulePost($id){
		$rules=I('post.rules','');
		$status=M('AuthGroup')->where("id={$id}")->save(array('rules'=>$rules));
		if($status) $this->success('赋予权限成功');
		else $this->error('赋予权限失败');
	}

	public function del($id){
		$return=M('AuthGroup')->delete($id);
		if($return) $this->success('删除成功');
		else $this->error('删除失败');
	}




}