<?php
namespace plugins\weixin\index;

use app\common\controller\IndexBase;


class Check extends IndexBase
{
    /**
     * 检查用户是否已关注公众号
     * @param number $uid
     */
    public function ifgz($uid=0,$type=''){
        if (empty($uid)) {
            $str = $this->user['weixin_api'];
            $uid = $this->user['uid'];
        }else{
            $str = $uid;
        }
        if (config('webdb.weixin_type')!=3) {
            return $this->err_js();
        }
        $result = wx_check_attention($str);
        if ($result===true) {
            if ($type=='set') {
                edit_user([
                        'uid'=>$uid,
                        'wx_attention'=>1
                ]);
            }
            return $this->ok_js([],'已关注');
        }elseif($result===false){
            if ($type=='set') {
                edit_user([
                        'uid'=>$uid,
                        'wx_attention'=>0
                ]);
            }
            return $this->err_js('还没关注公众号');
        }else{  //出错了
            return $this->err_js('错误:'.$result,[],2);
        }
    }
}