<?php
namespace app\admin\controller;

class AuthGroup extends Base
{	
    public function index()
    {
		$map = [];
		$input = input('name');
		if($input){
			$map['title|id'] = ['like','%'.$input.'%'];
		}
		$list = model('AuthGroup')->lists(12,$map);
		$this->assign('list',$list);
        return $this->fetch();
    }
	
	public function add(){
		if(request()->isPost()){
			$data = input();
			$auth_member = model('AuthGroup');
			$valid = \think\Loader::validate("AuthGroup")->scene('add');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if($auth_member->addGroup($data)){
					$this->success('添加成功',url('auth_group/index'));	
				}else{
					$this->error('添加失败');
				}
			}
		}else{
			return $this->fetch();
		}
	}
	
	public function edit(){
		if(request()->isPost()){
			$data = input();
			$auth_member = model('AuthGroup');
			$valid = \think\Loader::validate("AuthGroup")->scene('edit');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if($auth_member->editGroup($data)){
					$this->success('编辑成功',url('auth_group/index'));	
				}else{
					$this->error('编辑失败');
				}
			}
		}else{
			$id = input('id');
			$find = model('AuthGroup')->getFindOne($id);
			$this->assign('find',$find);
			return $this->fetch();
		}
	}
	
	public function state(){
		$id = input('id');
		$status = input('status');
		if($id && $status){
			$map = [
				'id' => ['in',$id],
			];
			if(model('AuthGroup')->stateGroup($map,['status'=>$status])){
				$this->success('设置成功');
			}else{
				$this->error('设置失败');
			}
		}else{
			$this->error('参数有误');
		}
	}
	
	public function remove(){
		$data = input('id');
		if($data){
			$auth_member = model('AuthGroup');
			if($auth_member->removeGroup($data)){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('提交的数据有误');
		}
		
	}
	
}
