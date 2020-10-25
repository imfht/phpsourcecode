<?php
/**
 * TXTCMS 登陆模块
 * @copyright			(C) 2013-2014 TXTCMS
 * @license			http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
class LoginAction extends AdminAction {
	public function _init(){
		parent::_init();
	}
	//显示模板
	public function index(){
		$this->display();
	}
	//登陆验证
	public function check(){
		$master=DB('master');
		$data=$master->where('id=1')->find();
		$ajax=array();
		if($_POST['username']==$data['name'] && md5($_POST['password'])==$data['pass']){
			$result=$master->where('id=1')->data(array('logtime'=>time(),'logip'=>$_SERVER['REMOTE_ADDR']))->save();
			$_SESSION['admin']['id']=$data['name'];
			$_SESSION['admin']['logtime']=$data['logtime'];
			$_SESSION['admin']['logip']=$data['logip'];
			if($_POST['autologin']=='yes') $_SESSION['admin']['auto']=$_POST['autologin'];
			$ajax['status'] = 1;
			$ajax['url'] = url('Admin/Index/index');
		}else{
			$ajax['status'] = 0;
			$ajax['info'] = '用户名或密码错误!';
		}
		$this->ajaxReturn($ajax);
	}
	// 登出
	public function out(){
		if (isset($_SESSION['admin']['id'])) {
			unset($_SESSION);
			session_destroy();
		}
		redirect(url('Home/Index/index'));
	}
}