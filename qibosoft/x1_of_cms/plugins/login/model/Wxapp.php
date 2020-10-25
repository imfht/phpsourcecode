<?php
namespace plugins\login\model;
use app\common\model\User AS UserModel;

//小程序用户注册
class Wxapp extends UserModel
{
    public static function api_reg($openid,$data=array()){
        if($data['nickName']==''){
            return 'nickName 昵称不存在！';
        }elseif($openid==''){
            return 'openid 值不存在！';
        }
        
        if( self::check_wxappIdExists( $openid ) ){
            return '当前微信号已经注册过了！';
        }
        
        $data['nickName'] = self::filterEmoji($data['nickName']);
        $username = $nickname = str_replace(array('|',' ','',"'",'"','/','*',',','~',';','<','>','$',"\\","\r","\t","\n","`","!","?","%","^"),'',$data['nickName']);
        
        $address = filtrate("{$data['province']} {$data['city']}");
        
        if(self::check_username($username)!==true){ //用户名不合法或者有非法字符
            $username='aa_'.rands(10);
        }elseif(strlen($username)>50||strlen($username)<2){
            
            //$username='bb_'.rands(7);
            $ts = self::where([])->order('uid','desc')->limit(1)->find();
            $ts['uid']++;
            $username = get_word($username,16,0).'_'.$ts['uid'];
        }
        
        $openid = filtrate($openid);
        //$username = filtrate($username);
        $icon = filtrate($data['avatarUrl']);
        $sex = $data['gender']=='男'?1:2;
        $address = filtrate($address);
        $groupid=8;
        
        //$username = get_word($username,40,0);	//帐号不能太长
        if(self::check_userexists($username)){	//检查用户名是否已存在
            $pss = self::where([])->order('uid','desc')->limit(1)->find();
            $username .='-'.($pss['uid']+1);
        }
        
        //随机生成邮箱与密码
        $password = rands(10);
        $email = rands(20).'@123.cn';
        
        $bday = $data['year']?$data['year'].'-00-00':'';
        
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
                'wxapp_api'=>$openid,
                'bday'=>$bday,
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