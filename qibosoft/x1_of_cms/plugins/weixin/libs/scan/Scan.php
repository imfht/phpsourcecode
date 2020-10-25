<?php 
namespace plugins\weixin\libs\subscribe;

use plugins\weixin\index\Api;
use plugins\weixin\model\User AS UserModel;

class Scan extends Api
{
    public function run(){
    }
    
    //扫码关注公众号时的事件
    private function subscribe_scan(){
        //扫码事件
        if(!preg_match("/^qrscene_/",$this->EventKey) ){
            return ;
        }
        $value = str_replace('qrscene_','',$this->EventKey);
        //对应这个页面的推荐人二维码推广/member/my2code.php
        if(NewUser===true){
            $uid_value = intval( str_replace('TZR','',$value) );
            $ts = UserModel::get_info(['uid'=>$uid_value]);
            if($ts){	//推荐人
                UserModel::edit_user([
                        'uid'=>$this->user['uid'],
                        'introducer_1'=>$ts['uid'],
                        'introducer_2'=>$ts['introducer_1'],
                        'introducer_3'=>$ts['introducer_2'],
                ]);
                //$lfjdb[introducer_1]=$ts[uid];	//下面这个函数要用到这个参数
                //weixin_hongbao_putIn(7);	//分享二维码推荐朋友关注公众号
            }
        }
        if($value==1){
            //scan_check_out();	//线下结算
        }elseif( preg_match("/^([0-9]+)$/",$value) ){	//用户登录 is_numeric($value)
            //scan_login($value);
        }else{
            //scan_action_type($value);	//其它扫描事件
        }
    }
    
    //扫描二维码登录PC网站
    private function scan_login($rand_id){
        global $db,$pre,$lfjdb,$timestamp,$user_appId,$webdb;
        $rand_id = intval($rand_id);
        $ac = wx_getAccessToken();
        $string=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=$ac&openid=$user_appId&lang=zh_CN");
        $data = json_decode($string,true);
        if($data[openid]==''){
            $MSG = '用户信息获取不到，登录失败';
        }else{
            $rsdb = $db->get_one("SELECT * FROM `{$pre}login_check` WHERE `rand_id`='$rand_id'");
            if($rsdb[url]){
                $data[UsrId] = $rsdb[usr];
                $str = http_Curl($rsdb[url],$data);
                if($str=='ok'){
                    $MSG = '首次操作,麻烦你在电脑端绑定旧帐号或者是自动注册一个新帐号
感谢你关注齐博公众号
今后将可以第一时间接收到官方的升级程序与相关安全补丁！';
                }elseif($str=='no'){
                    $MSG = '参数有误，请重新扫描！';
                }else{
                    $array = unserialize($str);
                    if($array[username]){
                        $MSG = "登录成功,
                        $array[username],欢迎你再次回来";
                    }else{
                        $MSG = '登录失败，服务器没响应！';
                    }
                }
            }else{
                $MSG = '网址不存在，登录失败！';
            }
        }
        send_wx_msg($user_appId,$MSG);
        exit;
    }
    
    
    //其它扫描事件
    private function scan_action_type($value){
        if( @ereg("^HY",$value) ){	//商家推广二维码
            require(ROOT_PATH."inc/scan_hy.php");
            scan_hy( str_replace('HY','',$value) );
        }
    }
    
    //扫一扫加关注，送红包，或者同时引导分享朋友圈
    /*
    function scan_give_hongbao(){
        global $db,$pre,$lfjdb,$webdb,$onlineip,$timestamp,$user_appId;
        
        if(!$lfjdb){
            $lfjdb = $userDB->weixin_reg($user_appId,'','');	//注册，数据入库
        }
        
        $money = $webdb[Scan_Money];
        $ifpay=0;
        
        $hongbao2DB = $db->get_one("SELECT * FROM `{$pre}wxhongbao` WHERE uid='$lfjdb[uid]' AND type='6' ORDER BY id DESC");
        
        if(!$hongbao2DB){
            if($money>=1 && Limit_map_check($lfjdb[weixin_api]) ){	//发红包
                //$_msg=weixin_hongbao_sendOut( array('uid'=>$lfjdb[uid],'id'=>$lfjdb[weixin_api],'money'=>$money,'title'=>'恭喜发财','name'=>$webdb[weixinHongbaoName]) );
                //if($_msg=='ok'){
                //	$ifpay=1;
                //}
            }
            
            add_rmb($lfjdb[uid],$money,0,'线下活动微信扫一扫获得红包');
            $ifpay=0;
            
            $db->query("INSERT INTO  `{$pre}wxhongbao` ( `uid` ,  `username` ,  `posttime` ,  `type` ,  `money`,  `ip`  `ifpay`) VALUES ( '$lfjdb[uid]',  '$lfjdb[username]',  '$timestamp',  '6',  '$money',  '$onlineip' , '$ifpay')");
        }else{
            send_wx_msg($user_appId,"重复扫描，不再派发红包");
        }
        
        if($webdb[weixin_scan_title]!=''&&$webdb[weixin_scan_link]!=''){	//图文并茂回复
            $array = array('title'=>$webdb[weixin_scan_title],'picurl'=>$webdb[weixin_scan_pic],'about'=>$webdb[weixin_scan_desc],'url'=>$webdb[weixin_scan_link]);
            echo give_news(array(0=>$array));
            //exit;
        }
        
    }*/
}