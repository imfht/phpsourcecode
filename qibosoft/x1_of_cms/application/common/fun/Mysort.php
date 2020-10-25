<?php
namespace app\common\fun;
use think\Db;

/**
 * 我的分类用到一些方法
 * @author Administrator
 *
 */
class Mysort{

    /**
     * 我的分类,根据UID获取
     * @param number $uid
     * @param string $mod 哪个频道 我的分类
     * @return array
     */
    public static function getByuid($uid=0,$mod=''){
        $mod || $mod=config('system_dirname');

        if (!class_exists("app\\".$mod."\\model\\Mysort")) {
            return ;
        }
        if (empty($uid)) {
            $uid = login_user('uid');
        }
        $map = [
            'uid'=>$uid,
        ];
        
        return Db::name($mod.'_mysort')->where($map)->order('list desc,id asc')->column(true);
    }
    
    /**
     * 我的分类,根据ext_id获取
     * @param number $ext_id
     * @param number $ext_sys
     * @param string $mod
     * @return array
     */
    public static function getByext($ext_id=0,$ext_sys=0,$mod=''){
        $mod || $mod=config('system_dirname');
        
        if (!class_exists("app\\".$mod."\\model\\Mysort")) {
            return ;
        }
        $map = [
                'ext_id'=>$ext_id,
        ];
        if ($ext_sys) {
            $map['ext_sys'] = $ext_sys;
        }
        return Db::name($mod.'_mysort')->where($map)->order('list desc,id asc')->column(true);
    }
    
}