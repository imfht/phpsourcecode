<?php

namespace app\lib;

use think\Session;

/**
 * 管理后台 用户登录 退出
 */
class Administrator
{
    //用户数据表
    public static $user = 'administrator';
    //错误信息
    public static $error = array(
        'usernameNotExist' => ['code'=>-1, 'msg'=>'用户名不存在'],
        'incorrectPassword' => ['code'=>-2, 'msg'=>'不正确的密码'],
    );

    //获取用户信息
    public static function userInfo()
    {
        return Session::get('serInfo', false);
    }

    //用户登录
    public static function login($username, $password)
    {
        $ret = db(self::$user)->where(['username'=>$username])->find();
        if(!$ret){
            return self::$error['usernameNotExist'];
        }
        if($ret['password'] !== self::encryptPassword($password)){
            return self::$error['incorrectPassword'];
        }

        Session::set('userInfo', $ret);
        return ['code'=>1, 'data'=>$ret];
    }

    //用户退出
    public static function logout()
    {
        Session::delete('userInfo');
        return true;
    }

    /**
     * 重置用户密码
     * @author baiyouwen 
     */
    public static function resetPassword($uid,$NewPassword)
    {
        $passwd = self::encryptPassword($NewPassword);
        $ret = db(self::$user)->where(['id'=>$uid])->update(['password'=>$passwd]);
        return $ret;
    }

    /**
     * 更新用户信息
     * @author baiyouwen 
     */
    public static function updateInfo($uid, $password, $data)
    {
        $passwd = self::encryptPassword($password);
        $ret = db(self::$user)->where(['id'=>$uid, 'password'=>$passwd])->update($data);
        return $ret;
    }

    // 密码加密
    public static function encryptPassword($password, $salt='', $encrypt='md5')
    {
        return $encrypt($password.$salt);
    }

}