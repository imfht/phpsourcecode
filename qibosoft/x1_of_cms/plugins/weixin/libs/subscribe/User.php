<?php 
namespace plugins\weixin\libs\subscribe;

use plugins\weixin\index\Api;
use plugins\weixin\model\User AS UserModel;

class User extends Api
{
    //针对原来已注册的老会员，新关注做个标志
    public function run(){
        $this->subscribe_attention();
    }
    
    /**
     * 针对原来已注册的老会员，新关注做个标志
     */
    protected function subscribe_attention(){
        if($this->user){
            if(!$this->user['wx_attention']){
                UserModel::edit_user([
                        'uid'=>$this->user['uid'],
                        'wx_attention'=>1,
                ]);
            }
        }
    }
    
}