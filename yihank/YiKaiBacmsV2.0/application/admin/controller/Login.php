<?php
namespace app\admin\controller;
use think\Controller;
class Login extends Controller{
    public function index(){
        if (input('post.')){
            $userName = input('post.username');
            $passWord = input('post.password');
            if(empty($userName)||empty($passWord)){
                $this->error('用户名或密码未填写！');
            }
            //查询用户
            $map = array();
            $map['username'] = $userName;
            $userInfo = model('AdminUser')->getWhereInfo($map);
            if(empty($userInfo)){
                return $this->error('登录用户不存在！');
            }
            if(!$userInfo['status']||!$userInfo['group_status']){
                return $this->error('该用户已被禁止登录！');
            }
            if($userInfo['password']<>md5($passWord)){
                return $this->error('您输入的密码不正确！');
            }
            $model = model('AdminUser');
            if($model->setLogin($userInfo['user_id'])){
                return $this->success('登录成功','index/index');
            }else{
                return $this->error('登录失败');
            }
        }else{
            return $this->fetch();
        }
    }
    /**
     * 退出登录
     */
    public function logout(){
        model('AdminUser')->logout();
        return ajaxReturn(200,'退出系统成功！',url('index'));
    }
}
