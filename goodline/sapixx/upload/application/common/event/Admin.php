<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 后台管理切换Cookie事件
 */
namespace app\common\event;
use think\facade\Cookie;
use think\facade\Session;
use encrypter\Encrypter;

class Admin{

    //管理登录
    const session_scope   = 'sapixx_com';                  //SESSION作用域
    const session_name    = 'sapixx_system_AklUhS0FTPcT';  //SESSION值

    //管理应用
    const cookie_miniapp  = 'admin_miniapp';          //Cookie名称
    const cookie_key      = 'admin_miniapp_key_var';  //Cookie名称

    /**
     * ##########################################
     * 判断管理员是否登录
     * @access public
     * @return boolean
     */
    public static function getLoginSession(){
        if(Session::has(self::session_name,self::session_scope)){
            return Session::get(self::session_name,self::session_scope);
        }
        return false;
    }

    
    /**
     * 登录后台管理Session
     * @access public
     */
    public static function setLoginSession($param){
        $data = [
            'username'   => $param['username'],
            'admin_id'   => $param['id'],
            'login_time' => time(),
        ];
        return Session::set(self::session_name,$data,self::session_scope);
    }

    /**
     * 退出后台管理Session
     * @access public
     */
    public static function setlogoutSession(){
        return Session::delete(self::session_name,self::session_scope);
    }


    /**
     * 以下是当前管理小程序
     * ########################################
     * 获取管理应用信息
     * @return void
     */
    public static function getMiniapp(){
        if(Cookie::has(self::cookie_miniapp)){
            $info = Cookie::get(self::cookie_miniapp);
            return json_decode(Encrypter::cpDecode($info,self::cookie_key),true);
        }
        return false;
    }

    /**
     * 设置管理应用信息
     * @access public
     */
    public static function setMiniapp($miniapp_id){
        $data['miniapp_id'] = $miniapp_id;
        $key = Encrypter::cpEncode(json_encode($data),self::cookie_key);
        return Cookie::set(self::cookie_miniapp,$key);
    }

    /**
     * 清空管理信息
     * @access public
     */
    public static function clearMiniapp(){
        return Cookie::delete(self::cookie_miniapp);
    }
}