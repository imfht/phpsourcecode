<?php
/**
 * 
 * 会员管理模块
 * @author Lain
 *
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;
class MemberController extends AdminController{
	public function _initialize(){
		$action = array(
				'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
				//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
	}
	
	public function manage(){

		//检索条件
		if(I('post.username')){
			$this->username = $username = I('post.username');
			$map['username'] = array('like', "%$username%");
		}
		if(I('post.roleid')){
			$this->roleid = $roleid = I('post.roleid');
			$map['roleid'] = $roleid;
		}
		//分页相关
		$page['pageCurrent'] = max(1 , I('post.pageCurrent'));
		$page['pageSize']= I('post.pageSize') ? I('post.pageSize') : 30 ;
		
		$totalCount = D('Member')->where($map)->count();
		$page['totalCount']=$totalCount ? $totalCount : 0;
			
		$this->page_list = D('Member')->order('userid')->where($map)->page($page['pageCurrent'], $page['pageSize'])->select();
		
		$this->page = $page;
		
		$this->display();
		
	}
	
	public function edit(){
		$userid = I('get.userid','','intval');
		$map['userid'] = $userid;
		$userinfo = D('Member')->where($map)->find();
		if(empty($userinfo)){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>L('parameter_error')));
		}

		if(IS_POST){
			$info = I('post.info');
			//如果密码不为空，修改用户密码。
			if(isset($info['password']) && !empty($info['password'])) {
				$info['password'] = password($info['password'], $userinfo['encrypt']);
			} else {
				unset($info['password']);
			}
			D('Member')->where($map)->save($info);
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'Member_manage'));
		}else{
			$this->assign('Detail', $userinfo);
			$this->display();
		}
	}
	public function add(){
		if(IS_POST){
			$info = I('post.info');
			//生成默认密码
			//$info = array_merge($info, password('1q2w3e4'));
			$info['encrypt'] = create_randomstr();
			$info['password'] = password($info['password'], $info['encrypt']);

			$result = D('Member')->add($info);
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'Member_manage'));
		}else{
	
			//获取角色
			$this->roles = S('role') ? S('role') : D('AdminRole')->get_role_list();
			$this->display('edit');
		}
	}



    //判断用户名是否重复
    public function ajax_checkUsername(){
        if(IS_GET){
        	$username = I('get.username');
        	$exist_username = D('Member')->where(array('username' => $username))->find();
        	if($exist_username){
        		echo '{"error":"用户名已存在"}';
        	}else {
        		echo '{"ok":""}';
        	}
        	exit;
        }
    }
}