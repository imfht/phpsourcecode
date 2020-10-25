<?php
/**
 * Created by JetBrains PhpStorm.
 * User: taotao
 * Date: 14-5-15
 * Time: 下午11:10
 * To change this template use File | Settings | File Templates.
 */

namespace Admin\Controller;


use Think\Controller;

class PublicController extends Controller{
    /**
     * Login Action登录操作
     */
    public function login(){
        if(IS_POST){
           $map['username'] = I("username");
           $map['password']  = md5(I("password"));
           $map['status'] = 1;
           $User = M("User");
           $result = $User->where($map)->select();
           if(!$result){
               $data = array("status"=>0,"info"=>"用户名或密码错误！");
               $this->ajaxReturn(json_encode($data));
           }else{
               session("uid",$result[0]["id"]);
               session("nickname",$result[0]["username"]);
               session("email",$result[0]["email"]);
               $data = array();
               $data['id']	=	$result[0]['id'];
               $data['last_login_time']	=	time();
               $data['last_login_ip']	=	get_client_ip();;
               $User->save($data);
               $data = array("status"=>1,"info"=>"登录成功！");
               $this->ajaxReturn(json_encode($data));
           }

        }else{

            $this->display();
        }
    }
    public function logout(){
        if(isset($_SESSION["uid"])) {
            unset($_SESSION);
            session_destroy();
            $this->success('退出成功',U('Public/login'),3);
        }else {
            $this->error('已经登出！');
        }
    }
}