<?php
use think\Model; 
use think\Request;
use think\Session;
class changyan extends Model{
    public function userinfo() {
        if(session('user')){
            $key="ffba3a79df4a4262c5531f74716e85b5";
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
    //数据回推
    /*public function pinglun() {
        if($_POST){
            $info=$_POST['data'];
            $info=json_decode($info,true);

            $data['url']=$info['url'];
            $data['email']=$info['comments'][0]['ip'];
            $data['uid']=$info['comments'][0]['user']['userid'];
            $data['createtime']=date('Y-m-d H:i:s',time());
            $data['content']=$info['comments'][0]['content'];
            $data['full_name']=$info['comments'][0]['user']['nickname'];
            $data['post_id']=$info['comments'][0]['referid'];//评论ID
            $data['pid']=$info['comments'][0]['replyid'];//回复的评论ID，没有为0
            M('comments')->add($data);

        }else{
            echo "页面错误";
        }
    }*/
}
?>
