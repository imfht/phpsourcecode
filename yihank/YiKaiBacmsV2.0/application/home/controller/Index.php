<?php
namespace app\home\controller;
class Index extends Site{
    //首页
    public function index(){
        //MEDIA信息
        $media=$this->getMedia();
        $this->assign('media', $media);
        $this->assign('crumb', array());
        //给模版给以一个当前时间戳的值
        $this->assign('demo_time',$this->request->time());
        return $this->siteFetch(get_site('tpl_index'));
    }
    //注册
    public function reg(){
        if (input('post.')){
            $status=model('User')->add();
            if ($status>0){
                return ajaxReturn(200,'注册成功',url('login'));
            }else{
                return ajaxReturn(0,'注册失败');
            }
        }else{
            return $this->siteFetch('reg');
        }
    }
    //登录
    public function login(){
        if (input('post.')){
            $email = input('post.email');
            $passWord = input('post.password');
            if(empty($email)||empty($passWord)){
                return ajaxReturn(0,'用户名或密码未填写！');
            }
            //查询用户
            $model = model('User');
            $map = array();
            $map['email'] = $email;
            $userInfo = $model->getWhereInfo($map);
            if(empty($userInfo)){
                return ajaxReturn(0,'登录用户不存在！');
            }
            if($userInfo['status']!=1){
                return ajaxReturn(0,'该用户已被禁止登录！');
            }
            if($userInfo['password']<>md5($passWord)){
                return ajaxReturn(0,'您输入的密码不正确！');
            }
            if($model->setLogin($userInfo)){
                return ajaxReturn(200,'登录成功',url('user/userHome'));
            }else{
                return ajaxReturn(0,'登录失败！');
            }
        }else{
            return $this->siteFetch('login');
        }
    }
    //退出登录
    public function loginOut(){
        model('User')->logout();
        return redirect('index/index');
    }
}
