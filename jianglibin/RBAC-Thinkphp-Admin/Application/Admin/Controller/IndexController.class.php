<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * @author jlb
 * @since 2016年12月7日09:57:17 
 */
class IndexController extends CommonController
{
	/**
	 * 显示后台首页
	 * @author  jlb
	 * @return [type] [description]
	 */
    public function index()
	{
		//未登录,则显示登录页
		if ( !$this->admin_id ) 
		{
			$this->display('login');
			die;
		}
		//将数组组装成树状结构
		$this->assign('menuList', array_tree($this->privilegeList,'menu_id'));
		$this->assign('uname', session('adminInfo.uname'));

		$this->display();
	}

	public function welcome()
	{
	}

	/**
	 * 后台登录
	 * @author jlb <[<email address>]>
	 * @return [type] [description]
	 */
	public function login()
	{
		if ( !IS_POST ) 
		{
			$this->error('非法请求','Index/index');
		}

		$uname = I('post.uname', '', 'trim');
		$pword = I('post.pword', '', 'trim');
		$verifycode = I('post.verifycode', '', 'trim');

		if ( !$uname ) 
		{
			$this->error('请输入账号');
		}
		if ( !$pword ) {
			$this->error('请输入密码');
		}
		if ( !$verifycode ) 
		{
			$this->error('请输入图形验证码');
		}
		elseif(!check_verify($verifycode, false))
		{
			$this->error('图形验证码不正确');
		}
		$whereLogin['uname'] = $uname;
		$whereLogin['pword'] = encrypt($pword);

		$user = M('Admin')->where($whereLogin)->find();

		if ( !$user ) 
		{
			$this->error('账号或者密码错误!');
		}

		//登录成功
		check_verify($verifycode); //失效验证码
		//添加进session
		session('adminInfo', $user);
		//跳转到首页
		$this->redirect('index');
	}


	/**
	 * 退出登录
	 * @return [type] [description]
	 */
	public function logout()
	{
		session_destroy();
		$this->redirect('index');
	}

}