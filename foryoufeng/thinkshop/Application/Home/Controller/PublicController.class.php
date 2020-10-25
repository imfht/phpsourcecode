<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\CPublicController;
use Think\Controller;
/**
 * 后台公共控制器
 * @author
 */
class PublicController extends CPublicController{
    /**
     * 后台登陆
     * @author jry <598821125@qq.com>
     */
    public function login(){
        if(IS_POST){
            $username = I('username');
            $password = I('password');
            //图片验证码校验
            /*if(!$this->check_verify(I('post.verify'))){
               $this->error('验证码输入错误！');
            }*/
            $manager=D('Manager');
            $map=array();//后台扩展信息
            $uid=$manager->login($username,$password,$map);
            if($uid){
                $this->success('登录成功！', U('admin/Index/index'));
            }else{
                if($manager->getError()=='1'){
                    $this->error('用户不存在或被禁用！');
                }else{
                    $this->error('密码错误！');
                }
            }
        }else{
            $this->success('来错了地方！', U('/'));
        }
    }

    /**
     * 注销
     * @author jry <598821125@qq.com>
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
        $this->success('退出成功！', U('/'));
    }
}
