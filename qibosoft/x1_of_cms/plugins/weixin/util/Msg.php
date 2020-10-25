<?php
namespace plugins\weixin\util;

class Msg
{
    protected $wxid;
    
    protected function getUrl($array=[]){
        if (empty($array[1])) {
            return ;
        }
        $token = '';
        $user = get_user($this->wxid,'weixin_api');
        if ($user) {
            $token = md5( $this->wxid . time() . $user['lastvist'] . rands(5) );
            cache($token,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",3600*24);
        }
        //once=1 出于安全考虑,限制只有一次有效
        if(strstr($array[2],'?')){
            $array[2].='&once=1&token='.$token;
        }else{
            $array[2].='?once=1&token='.$token;
        }
        return $array[1].':'.$array[2].$array[3];
    }
    
    public function send($openid,$content,$array=array()){
        
        if($openid=='' || config('webdb.weixin_type')<2){
            return ;
        }
        
        $this->wxid = $openid;
        $content = preg_replace_callback("/(http|https):([^ ]+)(\"|')/is",array($this,'getUrl'),$content);  //加入用户登录信息,让用户直接登录
        
        if($array['type']=='image'){
            $data="{
            \"touser\":\"$openid\",
            \"msgtype\":\"image\",
            \"image\":
            {
            \"media_id\":\"$array[id]\"
        }
        }";
        }elseif($array['type']=='voice'){
            $data="{
            \"touser\":\"$openid\",
            \"msgtype\":\"voice\",
            \"voice\":
            {
            \"media_id\":\"$array[id]\"
        }
        }";
        }elseif($array['type']=='video'){
            $array['thumb_media_id'] || $array['thumb_media_id']=$array['id'];
            $data="{
            \"touser\":\"$openid\",
            \"msgtype\":\"video\",
            \"video\":
            {
            \"media_id\":\"$array[id]\",
            \"thumb_media_id\":\"$array[thumb_media_id]\",
            \"title\":\"$array[title]\",
            \"description\":\"$array[description]\"
        }
        }";
        }else{
            strstr($content,'"') && $content = addslashes($content);
            $data="{
            \"touser\":\"$openid\",
            \"msgtype\":\"text\",
            \"text\":
            {
            \"content\":\"$content\"
        }
        }";
        }
        $ac = wx_getAccessToken();
        $string = http_curl("https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$ac",$data);
        if(strstr($string,'ok')){
            return true;
        }else{
            
            //超过48小时用户没访问过公众号的话，只能偿试用模板来给用户发消息
            if($array['type']!='image'&&$array['type']!='voice'&&$array['type']!='video'){
                $first = "你好!";
                $subject = "来自《".config('webdb.webname')."》的消息";
                $sender = "系统消息";
                
                $content = stripslashes($content);
                preg_match("/(http|https):([^ ]+)(\"|')/is",$content,$array);
                $url = $array[2] ? "$array[1]:$array[2]" : request()->url(true);
                $content = preg_replace('/<([^<]*)>/is',"",$content);
                $content = addslashes($content);
                
                //TM00440	新邮件通知
                $data="      {
                \"touser\":\"$openid\",
                \"template_id\":\"".config('webdb.weixin_msg_template_id')."\",
                \"url\":\"$url\",
                \"data\":{
                \"first\": {
                \"value\":\"$first\",
                \"color\":\"#0000ff\"
            },
            \"subject\":{
            \"value\":\"$subject\",
            \"color\":\"#666666\"
            },
            \"sender\": {
            \"value\":\"$sender\",
            \"color\":\"#666666\"
            },
            \"remark\":{
            \"value\":\"$content\",
            \"color\":\"#0000ff\"
            }
            }
            }";
                $string = http_Curl("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$ac",$data);
                if(strstr($string,'ok')){
                    return true;
                }
            }
            
            return $string;
        }
    }
}