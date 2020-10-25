<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户公众号登录Cookie事件处理
 */
namespace app\common\event;
use app\common\model\SystemMemberMiniapp;
use think\facade\Cookie;
use think\facade\Session;
use think\facade\Request;
use encrypter\Encrypter;
use filter\Filter;

class User{

    const session_scope = 'sapixx#com/user/scope';        //SESSION作用域
    const session_name  = 'sapixx#com/user/value';        //SESSION值
    const key           = 'sapixx#com@rXtvCz4E75Ai';  //加密秘钥

   /**
     * ##########################################
     * 判断管理员是否登录
     * @access public
     * @return boolean
     */
    public static function isLogin(){
        return Session::has(self::session_name,self::session_scope) ? true : false;
    }
    
    /**
     * 获取用户详细信息
     * @return boolean/array
     */
    public static function getUser(){
        if(self::isLogin()){
            $user = model('SystemUser')->where(['id' => self::getLogin('uid')])->find();
            if(empty($user)) return false;
            if($user['is_lock'] == 0){
                return $user;
            }
        }
        return false;
    }

    /**
     * 获取登录Session中保存的数据
     * @param string $key数组键名(user_id、username,ucode);
     * @return void
     */
    public static function getLogin(string $key = ''){
        if(self::isLogin()){
            $info = Session::get(self::session_name,self::session_scope);
            $login_info = json_decode(Encrypter::cpDecode($info,self::key),true);
            return empty($key) ? $login_info : $login_info[$key];
        }
        return false;
    }

    /**
     * 设置登录Session
     * @access public
     */
    public static function setLogin($param){
        $data = [
            'uid'        => $param['id'],
            'nickname'   => $param['nickname'],
            'login_time' => time(),
        ];
        $key = Encrypter::cpEncode(json_encode($data),self::key);
        return Session::set(self::session_name,$key,self::session_scope);
    }

    /**
     * 退出Session
     * @access public
     */
    public static function setlogout(){
        return Session::clear(self::session_scope);
    }


    /**
     * ##########################################
     * 设置邀请码
     * @access public
     */
    public static function setUcode(){
        $ucode = Request::param('ucode');
        if(isset($ucode)){
            return Cookie::set('ucode',Filter::filter_escape($ucode),3600);
        }
    }

    /**
     * 获取邀请码
     * @access public
     */
    public static function getUcode(){
        if(Cookie::has('ucode')){
            return Cookie::get('ucode');
        }
        return false;
    } 

    /**
     * 删除邀请码
     * @access public
     */
    public static function detUcode(){
        return Cookie::delete('ucode');
    } 

    /**
     * 判断创始人,并读取后绑定的创始人的信息
     * @access public
     */
    public static function isFounder($miniapp_id){
        $miniapp = SystemMemberMiniapp::where(['id' => $miniapp_id])->field('uid')->find();
        if(empty($miniapp->user)){
            $data['user_id'] = 0;
        }else{
            $data['user_id']      = $miniapp->user->id;
            $data['invite_code']  = $miniapp->user->invite_code;
            $data['phone_uid']    = $miniapp->user->phone_uid;
            $data['invite_code']  = $miniapp->user->invite_code;
            $data['face']         = $miniapp->user->face;
            $data['nickname']     = $miniapp->user->nickname;
            $data['miniapp_uid']  = $miniapp->user->miniapp_uid;
            $data['official_uid'] = $miniapp->user->official_uid;
            $data['login_time']   = date('Y-m-d',$miniapp->user->login_time);
        }
        return (object)$data;
    } 
}