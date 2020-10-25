<?php
namespace app\home\controller;
class Changyan extends Common{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function userinfo() {
        $root='http://'.$_SERVER['HTTP_HOST'];
        if(session('user')){
            $key=$this->changyan['app_key'];
            $imgUrl = imgUrl(session('user.avatar'));
            $nickname=session('user.username');
            $profileUrl="http://www.wen-world.com/user/index/index/id/".session('user.id').".html";
            $isvUserId=session('user.id');
            $toSign = "img_url=".$imgUrl."&nickname=".$nickname."&profile_url=".$profileUrl."&user_id=".$isvUserId;
            $signature = base64_encode(hash_hmac("sha1", $toSign, $key, true));
            $_SESSION['user']['sign']=$signature;
            $ret=array(
                "is_login"=>1, //已登录，返回登录的用户信息
                "user"=>array(
                    "user_id"=>session('user.id'),
                    "nickname"=>session('user.username'),
                    "img_url"=>$imgUrl,
                    "profile_url"=>$profileUrl,
                    "sign"=>$signature
                )
            );
        }else{
            $ret=array("is_login"=>0);//未登录
        }
        echo $_GET['callback'].'('.json_encode($ret).')';
    }

    //退出登录
    public function logout() {
        if(session('user')){
            session("user",null);
            $return=array(
                'code'=>1,
                'reload_page'=>1
            );
        }else{
            $return=array(
                'code'=>1,
                'reload_page'=>0
            );
        }
    }
}