<?php
namespace plugins\weixin\libs\keyword;

use plugins\weixin\index\Api;
use app\common\model\User AS UserModel;

class Bind extends Api
{
    public function run(){
		$this->bd();
    }
    
    protected function bd(){
        $content = $this->From_content;
        if(preg_match("/^bind([\d]+)$/i",$content,$array)){
            $uid = $array[1];
            $str = cache('bind'.$uid);
            if (!$str) {
                echo $this->give_text("不存在校验信息!");
                exit;
            }
            cache('bind'.$uid,null);
            $buser = get_user($uid);
            if (!$buser) {
                echo $this->give_text("该UID帐号不存在");
                exit;
            }
            $user = UserModel::get_info($this->user_appId,'weixin_api');
            if ($user['uid']!=$uid) {
                edit_user([
                    'uid'=>$user['uid'],
                    'weixin_api'=>'',
                    'wx_attention'=>0,
                ]);
                edit_user([
                    'uid'=>$uid,
                    'weixin_api'=>$this->user_appId,
                    'wx_attention'=>1,
                ]);
                $msg = "该帐号:“".$buser['username']."”成功绑定当前微信，原帐号:“".$user['username']."”已经解绑微信登录";
                if (!is_numeric($str)) {
                    $msg = '<a href="'.$str.'">'.$msg.'，请点击进行相关业务操作</a>';
                }
                echo $this->give_text($msg);
            }else{
                echo $this->give_text("请不要重复绑定");
            }
            exit;
        }
    }
    
}