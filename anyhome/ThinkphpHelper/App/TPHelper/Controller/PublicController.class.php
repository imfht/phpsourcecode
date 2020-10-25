<?php
namespace TPHelper\Controller;
use Think\Controller;
class PublicController extends Controller {

    public function login()
    {
    	$this->display();
    }

    public function ckLogin()
    {
    	$account = I('account');
    	$password = I('password');
    	if ($account != C('admin_account') || $password != C('admin_pw')) {
    		$this->error('用户名或密码不正确');
    	}
    	session('admin_account',$account);
    	session('admin_pw',$password);
    	redirect(U('Index/index'));
    }

    public function logout()
    {
        session(null);
        redirect(U('Public/login'));
    }
}