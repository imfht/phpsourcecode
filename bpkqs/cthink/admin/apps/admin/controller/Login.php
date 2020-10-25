<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Session;

class Login extends Controller{
    public function index(){
		if(request()->isPost()){
			$username = input('username');
			$password = input('password');
			if(!empty($username) && !empty($password)){
				$admin_info = model('AuthMember')->login($username,$password);
				if($admin_info){
					if($admin_info['status'] == '2'){
						$this->error('帐号被禁用');
					}else if($admin_info['is_remove'] == '1'){
						$this->error('帐号被删除');
					}else{
						//验证成功后，记录登录信息
						if(model('AuthMember')->updateLoginInfo($admin_info)){
							$this->success('登录成功');
						}else{
							$this->error('登录验证失败');
						}
					}
				}else{
					$this->error('帐号和密码有误!');
				}
			}
		}else{
			if(is_login()){
				header('location:'.url('index/index'));
			}else{
				$domain = config('url_domain');
				$this->assign('static',$domain.'/static/'.request()->module());
				return $this->fetch();
			}
		}
    }
	
	/**
	 * 退出登录
	 */
	public function logout(){
		Session::clear();
		Session::flush();
		Session::destroy();
		header('location:'.url('login/index'));
	}
}
