<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 前台管理切换Cookie事件
 */
namespace app\common\event;
use app\common\model\SystemMember;
use think\facade\Cookie;
use encrypter\Encrypter;

class Passport{

    const cookie_login          = 'passport_sapixx';  //Cookie名称
    const cookie_login_key      = 'passport_sapixx_TMaHtsBJ55bA';  //Cookie名称

    const cookie_miniapp        = 'miniapp';         //Cookie名称
    const cookie_miniappe_key   = 'miniapp_sapixx';  //Cookie名称
    const cookie_miniapp_times  = 86400;  //Cookie名称


   /**
     * ##########################################
     * 判断管理员是否登录
     * @access public
     * @return boolean
     */
    public static function isLogin(){
        return Cookie::has(self::cookie_login) ? true :false;
    }
    
    /**
     * 获取用户详细信息
     * @return boolean/array
     */
    public static function getUser(){
        if(self::isLogin()){
            $user = SystemMember::where(['id' => self::getLogin('user_id'),'is_lock' => 0])->find();
            if (empty($user)) {
                return false;
            }
            if($user['is_lock'] == 0){
                return $user;
            }
        }
        return false;
    }
    
    /**
     * 获取登录Cookie中保存的数据
     * @param string $key数组键名(user_id、username,ucode);
     * @return void
     */
    public static function getLogin(string $key = null){
        if(self::isLogin()){
            $info = Cookie::get(self::cookie_login);
            $login_info = json_decode(Encrypter::cpDecode($info,self::cookie_login_key),true);
            return is_null($key) ? $login_info : $login_info[$key];
        }
        return false;
    }

    /**
     * 设置登录Cookie
     * @param array
     * @access public
     */
    public static function setLogin($param){
        $data = [
            'user_id'    => $param['id'],
            'username'   => $param['username'],
            'login_time' => time(),
        ];
        $key = Encrypter::cpEncode(json_encode($data),self::cookie_login_key);
        return Cookie::set(self::cookie_login,$key);
    }

    /**
     * 退出Cookie
     * @access public
     */
    public static function setlogout(){
        return Cookie::delete(self::cookie_login);
    } 

    /**
     * ##########################################################
     * 获取管理应用信息
     * @param string $key数组键名(user_id、username,ucode);
     * @return void
     */
    public static function getMiniapp(){
        if(Cookie::has(self::cookie_miniapp) ){
            $info = Cookie::get(self::cookie_miniapp);
            return json_decode(Encrypter::cpDecode($info,self::cookie_miniappe_key),true);
        }
        return false;
    }

    /**
     * 设置管理应用信息
     * @access public
     */
    public static function setMiniapp($param){
        $data = [
            'member_id'         => $param['member_id'],
            'miniapp_id'        => $param['miniapp_id'],
            'member_miniapp_id' => $param['member_miniapp_id'],
        ];
        $key = Encrypter::cpEncode(json_encode($data),self::cookie_miniappe_key);
        return Cookie::set(self::cookie_miniapp,$key,self::cookie_miniapp_times);
    }

    /**
     * 清空管理信息
     * @access public
     */
    public static function clearMiniapp(){
        return Cookie::delete(self::cookie_miniapp);
    }
}