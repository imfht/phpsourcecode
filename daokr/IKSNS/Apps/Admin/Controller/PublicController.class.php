<?php
/*
* @copyright (c) 2012-3000 IKPHP All Rights Reserved
* @author 小麦
* @Email:810578553@qq.com
*/
namespace Admin\Controller;

/**
 * 后台首页控制器
 * @author 小麦 <810578553@vip.qq.com>
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录
     * @author 小麦 <810578553@vip.qq.com>
     */
	public function login() { 
	       if(is_admin_login()){
                $this->redirect('Index/index');
            }else{
                $this->display();
           }
	}
	// 用户登出
	public function logout() {
		if(is_admin_login()) {
			session('admin_auth', null);
        	session('admin_auth_sign', null);
			session('[destroy]');
			$this->success('退出成功！', U('login'));
		}else {
			$this->redirect('login');
		}
	}
	
	// 登录检测
	public function checkLogin() {
		if(empty($_POST['admin_email'])) {
			$this->error('用户Email帐号错误！');
		}elseif (empty($_POST['admin_password'])){
			$this->error('密码必须！');
		}
		//生成认证条件
		$map            =   array();
		// 支持使用绑定帐号登录
		$map['email']	= $_POST['admin_email'];
		$map["status"]	=	array('gt',0);
		$authInfo = M('admin')->where($map)->find();
		//使用用户名、密码和状态的方式进行认证
		if(false === $authInfo) {
			$this->error('帐号不存在或已禁用！');
		}else {
			/* 验证用户密码 */
			if($authInfo['password'] != admin_md5($_POST['admin_password'],C('DATA_AUTH_KEY'))) {
				$this->error('密码错误！');
			}else{
				//登录成功
				$session_auth = array('userid'=>$authInfo['userid'],'username'=>$authInfo['username']);	
		        session('admin_auth', $session_auth);
		        session('admin_auth_sign', data_auth_sign($session_auth));
				//保存登录信息
				M('admin')->where(array('userid'=>$authInfo['userid']))->save(array('last_time'=>time(), 'last_ip'=>get_client_ip()));
				$this->success('登录成功！',U('Index/index'));		        
			}
		}
	}

}
