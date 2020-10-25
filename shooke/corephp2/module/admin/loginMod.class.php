<?php
namespace module\admin;

use lib\Action, lib\RBAC;
/**
 * 登录
 */
class loginMod extends Action
{
    private $password = 'admin55567707';
    
    /**
     * 登陆信息录入
     */
    public function index()
    {
        $this->display();
    }
    
    /**
     * 登录操作
     */
    public function doLogin()
    {
        if($_POST['password']==$this->password){
            session_start();
            $_SESSION['admin'] = 1;
            $this->redirect(url('meeting/index'));
        }else{
            $this->error('密码错误');
        }
    }
    
    public function logout()
    {
        session_start();
        unset($_SESSION['admin']);
        session_destroy();
        $this->success('退出成功',url('login/index'));
    }
}