<?php
namespace app\common\fun;

//防攻击
class Ddos{

    /**
     * 新增时的防止
     * @param unknown $data
     * @return boolean
     */
    public function add($data=[]){
        $array = login_user();
        $uid = $array['uid'];
        if($array['groupid']==3){
            return true;
        }
        if ( cache('ddos_'.$uid) ) {
            return '请不要那么频繁的发表内容!';
        }
        cache('ddos_'.$uid,true,10);	//非管理员,10秒内不能频繁发表内容
        return true;
    }
    
    public function reply($data=[]){
        $array = login_user();
        $uid = $array['uid'];
        if($array['groupid']==3){
            return true;
        }
        if ( cache('ddos_reply_'.$uid) ) {
            return '请不要那么频繁的发表内容!';
        }
        cache('ddos_reply_'.$uid,true,5);	//非管理员,5秒内不能频繁回复内容
        return true;
    }

}