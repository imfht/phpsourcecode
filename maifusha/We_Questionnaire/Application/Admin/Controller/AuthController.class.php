<?php 
namespace Admin\Controller;
use Think\Controller;

/**
 * 处理登入登出
 */
class AuthController extends Controller
{
	protected function _initialize()
	{
		/* 登录页面没有布局 */
		layout(false);
	}

	/**
	 * 应用登录
	 */
	public function login()
	{
		/* 进入登录页面 */
		if( IS_GET ){
			$this->display();
			exit();
		}

		if( !I('post.account') || !I('post.password') )
			$this->error('请填写完整账号、密码');

		$this->loginTo(I('post.account'), I('post.password')) OR $this->error('账号密码不存在');
		$this->redirect('/Index/index'); //登录成功		
	}

	/**
	 * 应用登出
	 */
	public function logout()
	{
		session(null);
		$this->redirect('/Auth/login');
	}

	/**
	 * 尝试以某账号登录
	 * @return bool  登录成功返回true， 反之false
	 */
	public function loginTo($account, $password)
	{
		/* 超级管理员登录 */
		if( $account == C('super_user') AND $password == C('super_password') ){
			define('IS_ROOT', true);

			$super = array(
				'account'   =>  C('super_user'),
				'password'  =>  C('super_password')
			);
			loginAccount($super);

			return true;
		}

		/* 普通管理员登录 */
		$admin = D('Admins')->authenticate($account, $password);
		
		if( $admin ){ //认证通过
			$admin['password'] = $password; //明文密码覆盖掉MD5密文
			loginAccount($admin);	
			return true;
		}else{ //认证失败
			return false;
		}
	}

}
?>