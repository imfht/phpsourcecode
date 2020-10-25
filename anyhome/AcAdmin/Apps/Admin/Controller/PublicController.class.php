<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller {
    public function cklogin()
    {
        $account = I('account');
        $pw = I('password');
        if ($account =='' || $pw == '') {
            $this->error('请输入用户名或密码');
            return;
        }
        if ($account != 'admin') {
            $this->error('登陆账号错误');
        }
        $password = C('ADMIN_PWD');
        if ($pw != $password) {
            $this->error('登陆密码错误');
        }
        session('admin_id',1);
        session('admin',true);
        $this->success('登录成功，转到主页', U('Admin/Index/index'));

        
    }

    public function logout()
    {
        session(NULL);
        $this->success('登出成功','index.php');
    }
}