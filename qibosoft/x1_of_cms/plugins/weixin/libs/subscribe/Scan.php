<?php 
namespace plugins\weixin\libs\subscribe;

use plugins\weixin\index\Api;
use plugins\weixin\model\User AS UserModel;

class Scan extends Api
{
    public function run(){
    }
    
    //扫码关注公众号时的事件
    protected function subscribe_scan(){
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
}