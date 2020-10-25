<?php
namespace app\admin\controller;

/**
 * 管理员控制器
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class AuthMember extends Base
{	
    public function index(){
		$input = input('name');
		$map = ['is_remove' => 0];
		if($input){
			$map['username|uid|nickname'] = ['like','%'.$input.'%'];
		}
		$list = model('AuthMember')->lists($map);
		$this->assign('list',$list);
        return $this->fetch();
    }
	
	public function add(){
		if(request()->isPost()){
			$data = input();
			$auth_member = model('AuthMember');
			$valid = \think\Loader::validate("AuthMember")->scene('add');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if($auth_member->addMember($data)){
					$this->success('添加成功',url('auth_member/index'));	
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
			$auth_member = model('AuthMember');
			$valid = \think\Loader::validate("AuthMember")->scene('edit');
			if(!$valid->check($data)){
				$this->error($valid->getError());
			}else{
				if($auth_member->editMember($data,['uid'=>$data['uid']])){
					$this->success('编辑成功',url('auth_member/index'));	
				}else{
					$this->error('编辑失败');
				}
			}
		}else{
			$uid = input('uid');
			$find = model('AuthMember')->getFindOne($uid);
			$this->assign('find',$find);
			return $this->fetch();
		}
	}
	
	public function state(){
		$uid = input('uid');
		$status = input('status');
		if($uid && $status){
			$map = [
				'uid' => ['in',$uid],
			];
			if(model('AuthMember')->stateMember($map,['status'=>$status])){
				$this->success('设置成功');
			}else{
				$this->error('设置失败');
			}
		}else{
			$this->error('参数有误');
		}
	}
	
	public function remove(){
		$data = input('uid');
		if($data){
			$auth_member = model('AuthMember');
			if($auth_member->removeMember($data)){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('提交的数据有误');
		}
		
	}
	
	public function tgauthor(){
		if(request()->isPost()){
			$data = input();
			if(model('AuthGroupAccess')->tagAccess($data['uid'],$data['id'])){
				$this->success('授权成功',url('auth_member/index'));
			}else{
				$this->error('授权失败');
			}
		}else{
			$uid = input('uid');
			$list = model('AuthGroup')->items();
			$mgs = model('AuthGroupAccess')->getMembaerGroups($uid);
			$checked = [];
			if($mgs){
				foreach($mgs as $value){
					$checked[] = $value['group_id'];
				}
			}
			$this->assign('mgs',$checked);
			$this->assign('list',$list);
			$this->assign('uid',$uid);
			return $this->fetch();
		}
		
	}
	
}
