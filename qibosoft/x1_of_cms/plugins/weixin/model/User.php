<?php
namespace plugins\weixin\model;
use app\common\model\User AS UserModel;

class User extends UserModel
{
    /**
     * 过滤掉微信emoji表情
     * @param unknown $str
     * @return mixed
     */
    protected static function filterEmoji($str){
        $str=preg_replace_callback('/./u',function($match){
        return strlen($match[0]) >= 4 ? '' :$match[0];
        },$str);
        return $str;
    }
    
    //微信用户注册
    public static function weixin_reg($openid,$data=array(),$Marray=array()){
        
        $check_attention = $Marray['check_attention'];
        $introducer_1 = $Marray['introducer_1'];
        $introducer_2 = $Marray['introducer_2'];
        $introducer_3 = $Marray['introducer_3'];
        
        if($openid && empty($data)){    //已经关注过，但还没有在系统注册的粉丝用户
            $ac = wx_getAccessToken();
            $string = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$ac.'&openid='.$openid.'&lang=zh_CN');
            $data = json_decode($string,true);
            $check_attention = 1;
        }
        
        if($data['nickname']==''){
            return 'nickname 昵称不存在！';
        }elseif($openid==''){
            return 'openid 值不存在！';
        }
        
        if( self::check_wxIdExists( $data['openid'] ) ){
            return '当前微信号已经注册过了！';
        }
        
        $data['nickname'] = self::filterEmoji($data['nickname']);
        $data['nickname'] = str_replace(array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^"),'',$data['nickname']);
        $username = $nickname = self::filterEmoji($data['nickname']);
        
        $address = filtrate("{$data['country']} {$data['province']} {$data['city']}");
        
        
        if(self::check_username($username)!==true){ //用户名不合法或者有非法字符
            $username='aa_'.rands(10);
        }elseif(strlen($username)>50||strlen($username)<2){
            
            //$username='bb_'.rands(7);
            $ts = self::where([])->order('uid','desc')->limit(1)->find();
            $ts['uid']++;
            $username = get_word($username,16,0).'_'.$ts['uid'];
        }
        
        $weixin_id = filtrate($data['openid']);
        $username = filtrate($username);
        $icon = filtrate($data['headimgurl']);
        $sex = intval($data['sex']);
        $address = filtrate($address);
        $groupid=8;
        
        //$username = get_word($username,40,0);	//帐号不能太长
        if(self::check_userexists($username)){	//检查用户名是否已存在
            $pss = self::where([])->order('uid','desc')->find();
            $username .='-'.($pss['uid']+1);
        }
        
        //随机生成邮箱与密码
        $password = rands(10);
        $email = rands(20).'@123.cn';
        
        $array = array(
                'unionid'=>$data['unionid']?filtrate($data['unionid']):'',
                'username'=>$username,
                'nickname'=>$nickname,
                'password'=>$password,
                'email'=>$email,
                'groupid'=>$groupid,
                'icon'=>$icon,
                'yz'=>1,
                'lastvist'=>time(),
                'lastip'=>get_ip(),
                'regdate'=>time(),
                'regip'=>get_ip(),
                'sex'=>$sex,
                'address'=>$address,
                'weixin_api'=>$weixin_id,
                'introducer_1'=>$introducer_1,
                'introducer_2'=>$introducer_2,
                'introducer_3'=>$introducer_3,
                'wx_attention'=>$check_attention,
                //'pageid'=>intval($Marray['pageid']),
        );
        
        //入库
        $uid = self::register_user($array);
        if($uid<1){
            return $uid;
        }
        
        $array['uid'] = $uid;
        return $array;
    }
}