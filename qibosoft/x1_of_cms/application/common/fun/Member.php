<?php
namespace app\common\fun;

/**
 * 用户的相关函数
 */
class Member{
    /**
     * 格式化用户数据,主要是把一些敏感数据过滤掉不显示
     * @param array $rs 用户数据
     * @param number $uid 当前用户登录UID
     * @return unknown
     */
    public static function format($rs=[],$uid=0){
        $rs['icon'] = $rs['icon']?tempdir($rs['icon']):'';
        $rs['group_name'] = getGroupByid($rs['groupid']);
        $rs['lastvist'] = format_time($rs['_lastvist']=$rs['lastvist'],true);
        $rs['regdate'] = format_time($rs['_regdate']=$rs['regdate'],'Y-m-d H:i');
        unset($rs['password'],$rs['password_rand'],$rs['qq_api'],$rs['weixin_api'],$rs['wxapp_api'],$rs['unionid'],$rs['wxopen_api'],$rs['config'],$rs['rmb_pwd'],$rs['regip'],$rs['email'],$rs['address'],$rs['mobphone'],$rs['idcard'],$rs['idcardpic'],$rs['lastip'],$rs['ext_field']);
        if ($rs['uid']!=$uid) {
            unset($rs['rmb'],$rs['truename']);
        }
        return $rs;
    }
}