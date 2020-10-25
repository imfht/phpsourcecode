<?php
namespace app\system\controller;
/*
*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/3
*/
use app\system\model\User;
use think\Controller;
use think\Loader;
use think\Session;

class Login extends Controller {

    //login页面
    public function index() {
        if(Session::has('system_user')){
            $this->redirect('/system');
        }
        return $this->view->fetch();
    }

    public function login() {
        $post_data = $this->request->param();
        $validate = Loader::validate('Login');
        if(!$validate->check($post_data)){
            return getMsg($validate->getError());
        }
        if(!$info = User::get(['username'=>$post_data['user']])){
            return getMsg('用户名或密码错误');
        }

        if($info['password'] != auth_password($post_data['pwd'])){
            return getMsg('用户名或密码错误');
        }

        Session::set('system_user', $info);
        return getMsg('登录成功', url('index'));

    }

    /**
     * 退出登录
     */
    public function out() {
        Session::delete('system_user');
        $this->redirect('index');
    }

}