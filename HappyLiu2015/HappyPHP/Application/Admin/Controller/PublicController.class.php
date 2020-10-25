<?php

namespace Admin\Controller;
use Think\Controller;


/**
 * 后台登录登出控制器
 */
class PublicController extends Controller {

    /**
     * 后台用户登录
     *
     * @param null $username
     * @param null $password
     * @param null $verify
     */
    public function login($username = null, $password = null, $verify = null){
        if(IS_POST){
            /* 检测验证码 */
            if (APP_DEBUG == false){
                if(!check_verify($verify)){
                    $this->error('验证码输入错误！');
                }
            }

            //登录用户
            $User = D('User');
            if($User->login($username, $password)) { //登录用户
                //TODO:跳转到登录前页面
                $this->success('登录成功！', U('Index/index'));
            } else {
                $this->error($User->getError());
            }
        } else {
            $this->display();
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            D('User')->logout();
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }

    /**
     * 获取验证码
     */
    public function verify(){
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

}