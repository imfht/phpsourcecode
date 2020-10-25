<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
	public function _initialize(){
		$this->title="代码轮子";
	}
    public function login(){
        $this->display();
    }
	
	public function do_login(){
		$p=I("post.");
		$con['email']=$p['email'];
		$con['password']=md5($p['password']);
		$tmp=M('lz_userinfo')->where($con)->find();
		if($tmp){
			set_session($tmp);
			$out['ok']=1;
			$out['url']=U('Index/index');
		}else{
			$out['ok']=2;
		}
		$this->ajaxReturn($out,'JSON');
	}
	
	public function do_reg(){
		$p=I("post.");
		$con['email']=$p['email'];
		$tmp=M('lz_userinfo')->where($con)->find();
		if($tmp){
			$out['ok']=2;
		}else{
			$p['password']=md5($p['password']);
			M('lz_userinfo')->add($p);
			$out['ok']=1;
		}
		$this->ajaxReturn($out,'JSON');
	}
	
	public function set_tags(){
		$this->title="【标签设置】-代码轮子";
		$this->rs=M('lz_tags')->select();
		$this->display();
	}
	
	public function post_set_tags(){
		$p=I("post.");
		M('lz_tags')->add($p);
		header('Location: '.U('User/set_tags'));
	}
	
	public function login_out(){
		session_unset();
		session_destroy();
		$this->success('退出登录！', U('Index/index'));
	}
}