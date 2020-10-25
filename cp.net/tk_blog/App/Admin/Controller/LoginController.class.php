<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
        if (!empty($_SESSION['admin_user']['uid'])) {
            redirect('/Admin');
        }
        $this->display();
    }

    //处理登录
    public function send_login(){
        if (IS_AJAX) {
            $user = trim(I('post.username'));
            $pwd = trim(I('post.password'));
            if (empty($user) || empty($pwd)) {
                exit(json_encode(array('status'=>0,'msg'=>'用户名或密码必须填写.^_^')));
            }
            $UserModel = M('Users');
            $where = array('uname'=>$user,'user_type'=>1);
            $userData = $UserModel->where($where)->find();
            if (!$userData || $userData['password'] != encrypt_password($pwd)) {
                exit(json_encode(array('status'=>0,'msg'=>'用户名或密码错误.^_^')));
            }
            //修改最后的登录时间和ip
            $data = array(
                'last_login_ip' => get_client_ip(0,true),
                'last_login_time' => time()
            );
            $UserModel->where($where)->save($data);
            $_SESSION['admin_user'] = $userData;
            $_SESSION['user'] = $userData;
            exit(json_encode(array('status'=>1,'msg'=>'登录成功.^_^','url'=>'/Admin')));
        }
    }

    //退出
    public function loginOut(){
        session_unset();
        session_destroy();
        redirect('/Admin');
    }
}