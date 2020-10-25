<?php
namespace app\home\controller;
class User extends CheckUser{
    //首页
    public function userHome(){
        return $this->siteFetch('user_home');
    }
    //设置
    public function userSet(){
        if (input('post.')){
            $status=model('User')->edit();
            if ($status!==false){
                return ajaxReturn(200,'操作成功');
            }else{
                return ajaxReturn(0,'操作失败');
            }
        }else{
            return $this->siteFetch('user_set');
        }
    }
    //修改密码
    public function editPassword(){
        if (empty(input('password'))||empty(input('password2'))||empty(input('nowpassword'))){
            return ajaxReturn(0,'参数不足');
        }
        if (input('password')!=input('password2')){
            return ajaxReturn(0,'两次密码不一致');
        }
        $user_info=model('User')->getInfo(session('home_user.user_id'));
        if ($user_info['password']<>md5(input('nowpassword'))){
            return ajaxReturn(0,'原密码不正确');
        }
        $status=model('User')->editPassword();
        if ($status!==false){
            return ajaxReturn(200,'操作成功');
        }else{
            return ajaxReturn(0,'操作失败');
        }
    }
}
