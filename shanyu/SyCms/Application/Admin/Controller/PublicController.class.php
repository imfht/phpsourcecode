<?php
namespace Admin\Controller;
use Think\Controller;

class PublicController extends Controller {
	public function login(){

        if(IS_POST){ $this->loginPost();exit;}
    	
		if(is_login()) $this->redirect('/Admin');
        
		$this->display();
	}
    private function loginPost(){
        //默认过滤
        $code=I('post.code','');
        $username=I('post.username','');
        $password=I('post.password','');
        $remember=I('remember',0,'intval');

        //常规检测
        if(empty($username) || empty($password))$this->error('账号和密码不能为空');

        //检测验证码
        if(empty($code) || !$this->checkVerify($code)) $this->error('验证码填写错误');

        //登录检测
        $admin=D('Admin')->login($username,$password,$remember);
        if(!$admin) $this->error(D('Admin')->getError());

        $this->success('登录成功',U('/Admin'));
    }

    //退出登录
    public function logout(){
        session('[destroy]');
        cookie('admin_token',null);
        $this->redirect('/Admin');
    }

    //输出验证码
    public function verify(){
        $config=array(
            'length'=>4,
            'fontSize'=>24,
            'useCurve'=>false,
            'useNoise'=>true,
            'fontttf' => '2.ttf',
            'codeSet' => '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ',
        );
        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }

    private function checkVerify($code){
        $Verify = new \Think\Verify();
        if(!$Verify->check($code, 1)) return false;
        return true;
    }



}