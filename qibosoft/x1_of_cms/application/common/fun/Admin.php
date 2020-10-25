<?php
namespace app\common\fun;

/**
 * 权限相关
 */
class Admin{
    /**
     * 查找栏目管理员及频道管理员
     * @param number $fid
     * @param string $dirname
     * @return boolean
     */
    public static function sort($fid=0,$dirname=''){
        $user = login_user();
        if ($user['groupid']==3) {
            return true;
        }
        if (empty($dirname)) {
            $dirname = config('system_dirname');
        }
        $str = config('webdb.admin');
        if ($fid) {
            $str .= ','.get_sort($fid,'admin','',$dirname);
        }        
        foreach(explode(',',$str) AS $uid){
            if($uid>0 && $uid==$user['uid']){
                return true;
            }
        }
    }
}