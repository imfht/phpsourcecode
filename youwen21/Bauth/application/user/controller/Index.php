<?php

namespace app\user\controller;

use think\Controller;
use youwen\think_user\User;
use youwen\think_user\UserCode;


/**
 */
class Index extends Controller
{
    /** 
     * 个人中心
     * @return [type] [description]
     * @author baiyouwen
     */
    public function index()
    {
        $obj = \youwen\think_user\LoginSession::getDriver('pc');
        $ret = $obj->isLogin();
        if(!$ret){
            $this->redirect(url('login'));
        }
        $this->assign('userRow', \think\Session::get('userRow'));
        return $this->fetch();
    }
    /** 
     * 登录
     * @return [type] [description]
     * @author baiyouwen
     */
    public function login()
    {
        if($this->request->isPost()){
            // echo '<pre>';
            // print_r( input('post.') );
            // exit('</pre>');
            $username = input('post.username');
            $password = input('post.password');
            $user = new User();
            $ret = $user->login($username, $password);
            if(0 !== $ret){
                $msg = $user->getErrorMsg($ret);
                $this->error($msg);
            }

            $userDetail = $user->getUserDetail();

            $obj = \youwen\think_user\LoginSession::getDriver('pc');

            $logRet = $obj->setLoginSession($userDetail);
            if(0 !== $logRet){
                $this->error($obj->getErrorMsg($logRet));
            }
            $this->redirect(url('index'));
        }else{
            return $this->fetch();
        }
    }

    /** 
     * 注册
     * @return [type] [description]
     * @author baiyouwen
     */
    public function register()
    {
        if($this->request->isPost()){
            $username = input('username');
            $password = input('password');
            $rePassword = input('rePassword');
            $user =  new User();
            $ret = $user->regsiter($username, $password, $rePassword);
            if(0 !== $ret){
                $this->error($user->getErrorMsg($ret));
            }
            return $this->success('注册成功', url('login'));
        }else{
            return $this->fetch();
        } 
    }

    /** 
     * 退出登录
     * @return [type] [description]
     * @author baiyouwen
     */
    public function logout()
    {
        $obj = \youwen\think_user\LoginSession::getDriver('pc');
        $ret = $obj->logout();
        if(!$ret){
            $this->redirect(url('login'));
        }else{
            $this->error($obj->getErrorMsg($ret));
        }
    }
}
