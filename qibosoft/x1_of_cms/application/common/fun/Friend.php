<?php
namespace app\common\fun;

use app\common\model\Friend AS Model;

/**
 * 好友相关
 */
class Friend{

    /**
     * 查询对方属于我的哪种好友
     * @param number $suid
     * @param number $myuid
     * @return array|NULL[]|unknown
     */
    public static function my($suid=0,$myuid=0){
        $myuid || $myuid = login_user('uid');
        $map = [
                'suid'=>$suid,
                'uid'=>$myuid,
        ];
        return getArray(Model::where($map)->find())?:[];
    }
}