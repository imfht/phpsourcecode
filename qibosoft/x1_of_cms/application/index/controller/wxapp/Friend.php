<?php
namespace app\index\controller\wxapp;

use app\common\model\Friend AS Model;
use app\common\controller\IndexBase;

//小程序  
class Friend extends IndexBase{
    
    /**
     * 列出某个用户的好友与粉丝
     * @param number $uid 
     * @param number $suid
     * @param number $type 1是粉丝,-1黑名单,2是好友(未必是双向好友,他是我的好友,但我可能是他的黑名单或者他还没关注我)
     * @param number $rows
     * @param number $page
     * @return void|unknown|\think\response\Json
     */
    public function get_list($uid=0,$suid=0,$type='',$rows=20,$page=1){
        $map = [];
        if ($uid>0) {
            $map['uid'] = $uid;
        }elseif($suid>0){
            $map['suid'] = $suid;
        }else{
            return $this->err_js('参数有误');
        }
        if (is_numeric($type)) {
            $map['type'] = $type;
        }elseif($type!=''){
            $array = [];
            $detail = explode(',',$type);
            foreach ($detail AS $value){
                if (is_numeric($value)) {
                    $array[] = $value;
                }
            }
            if ($array) {
                $map['type'] = ['in',implode(',', $array)];
            }
        }        
        $array = Model::where($map)->order('update_time desc,id desc')->paginate($rows);
        $array = getArray($array);
        foreach($array['data'] AS $key=>$rs){
            if($suid){
                $rs['he_id'] = $rs['uid'];
            }else{
                $rs['he_id'] = $rs['suid'];
            }
            $rs['he_username'] = get_user_name($rs['he_id']);
            $rs['he_icon'] = get_user_icon($rs['he_id']);
            $rs['he_lastvist'] = format_time(get_user($rs['he_id'])['lastvist'],true);
            
            $array['data'][$key] = $rs;
        }
        return $this->ok_js($array);
    }
    
}
