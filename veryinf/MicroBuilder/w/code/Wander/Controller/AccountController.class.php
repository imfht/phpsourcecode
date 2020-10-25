<?php
/**
 * 用户会话
 */
namespace Wander\Controller;
use Core\Model\Acl;
use Core\Model\Utility;
use Think\Controller;
class AccountController extends Controller {

    public function registerAction() {
        $this->success('注册页面');
    }

    public function loginAction() {
        session_start();
        if(IS_POST) {
            $username = I('post.username');
            $password = I('post.password');
            if(empty($username) || empty($password)) {
                $this->error('请输入用户名及密码');
            }
            $acl = new Acl();
            $user = $acl->getUser($username, true);
            if(!empty($user)) {
                $pwd = Utility::encodePassword($password, $user['salt']);
                if($pwd != $user['password']) {
                    $this->error('您输入的密码错误');
                }
                if($user['status'] == Acl::STATUS_DISABLED) {
                    $this->error('您的账号已经被禁用, 请联系系统管理员');
                }
                $user = coll_elements(array('uid', 'username', 'role'), $user);
                session('user', $user);
                $forward = I('get.forward');
                if(empty($forward)) {
                    $forward = U('bench/welcome/index');
                } else {
                    $forward = base64_decode($forward);
                }
                $this->success('成功登陆', $forward);
            } else {
                $this->error('您输入的用户名或密码错误');
            }
            exit;
        }
        $this->display('Wander/login');
    }

    public function logoutAction() {
        session_start();
        session(null);
        $this->success('成功退出系统');
    }
}