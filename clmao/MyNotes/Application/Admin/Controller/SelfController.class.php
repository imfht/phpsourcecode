<?php

namespace Admin\Controller;

use Think\Controller;

class SelfController extends CommonController {
    //修改密码
    public function updatePwd() {
        if(IS_POST){
            $data = $_POST;
            $user = D('User');
            if(!$user->create($data,2)){
                $this->error($user->getError());
            }else{
               $clean = array();
               $clean['user'] = I('post.user');
               $clean['pwd'] = clmao_md5_half(I('post.pwd2'));
               if(M('user')->where(array('user'=>$clean['user']))->save($clean)){
                   $this->success('密码修改成功');
                   die;
               }else{
                   $this->error('密码修改失败');
                   die;
               }
            }
            
        }
        $this->display();
    }
}
