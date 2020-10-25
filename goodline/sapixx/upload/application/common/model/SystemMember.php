<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户管理
 */

namespace app\common\model;
use app\common\model\SystemMemberMiniapp;
use app\common\facade\Alisms;
use think\Model;

class SystemMember extends Model{

    protected $pk = 'id';

    /**
     * 用户信息关联
    * @return void
     */
    public function miniapp(){
        return $this->hasOne('SystemMemberMiniapp','id','bind_member_miniapp_id');
    }

    /**
     * 添加编辑
     * @param  array $param 数组
     */
    public static function edit(array $param){
        $data['phone_id']    = $param['phone_id'];
        $data['username']    = $param['username'];
        $data['lock_config'] = $param['lock_config'];
        $data['login_ip']    = request()->ip();
        $data['login_time']  = time();
        $data['update_time'] = time();
        if(!empty($param['login_password'])){
            $data['password']  = password_hash(md5($param['login_password']),PASSWORD_DEFAULT);
        }
        if(!empty($param['safe_password'])){
            $data['safe_password'] = password_hash(md5($param['safe_password']),PASSWORD_DEFAULT);
        }
        if(isset($param['id']) && $param['id'] > 0){
            return self::where('id',$param['id'])->update($data);
        }else{
            $data['create_time']   = time();
            $data['parent_id']     = 0;
            return self::insert($data);
        }
    }

    /**
     * 添加编辑
     * @param  array $param 数组
     */
    public static function bindEdit(array $param){
        $data['id']                     = !empty($param['id']) ? $param['id'] : 0 ;
        $data['phone_id']               = $param['phone_id'];
        $data['username']               = $param['username'];
        $data['password']               = password_hash(md5($param['login_password']), PASSWORD_DEFAULT);
        $data['auth']                   = intval($param['auth']);
        $data['bind_member_miniapp_id'] = $param['miniapp_id'];
        if(isset($param['id']) && $param['id'] > 0){
            return self::update($data);
        }else{
            $data['parent_id']              = $param['user_id'];
            $data['login_ip']               = request()->ip();
            $data['login_time']             = time();
            $data['update_time']            = time();
            $data['create_time']            = time();
            return self::create($data);
        }
    }

    /**
     * 更新用户新的手机号
     * @param  array $param 数组
     */
    public static function editPhone(array $param){
        $data['phone_id']    = $param['phone_id'];
        $data['update_time'] = time();
        return self::where('id',$param['id'])->update($data);
    }

    /**
     * 用户注册
     * @param  array $param 参数
     */
    public static function reg(array $param){
        //判断验证码
        if(!Alisms::isSms($param['phone_id'],$param['sms_code'])){
            return ['code'=>0,'message'=>"验证码错误"];
        }
        //判断手机号是否重复
        $info = self::where(['phone_id'=>$param['phone_id']])->find();
        if(isset($info)){
            return ['code'=>0,'message'=>'帐号已被注册'];
        }
        //验证码通过
        $data['phone_id']      = $param['phone_id'];
        $data['username']      = $param['username'];
        $data['password']      = password_hash(md5($param['login_password']),PASSWORD_DEFAULT);
        $data['safe_password'] = password_hash(md5('123456'),PASSWORD_DEFAULT);  //设置初始安全密码
        $data['login_time']    = time();
        $data['login_ip']      = request()->ip();
        $data['update_time']   = time();
        $data['create_time']   = time();
        $last_id =  self::insertGetId($data);
        if($last_id){
            return ['code'=>200,'message'=>'注册成功','data' => ['id' => $last_id]];
        }
        return ['code'=>0,'message'=>'帐号登录失败'];
    }

    /**
     * 修改我的密码
     * @access public
     */
    public static function upDatePasspowrd(int $uid,string $safepassword){
        $data['id']       = $uid;
        $data['password'] = password_hash(md5($safepassword),PASSWORD_DEFAULT);
        return self::update($data);
    } 
    
    /**
     * 登录用户
     * @param  array $param 更新的用户信息
     */
    public static function login(array $param){
        $condition['phone_id'] = $param['login_id'];
        $condition['is_lock'] = 0;
        $result = self::where($condition)->find();
        if($result){
            if(!password_verify(md5($param['login_password']),$result->getAttr('password'))) {
                return FALSE;
            }
            self::updateLogin($result->getAttr('id'));
            return $result;
        }
        return FALSE;
    }

     /**
     * 用户找回密码
     * @param  array $param 更新的用户信息
     */
    public static function getPasspord(array $param){
        $condition['phone_id'] = $param['phone_id'];
        $condition['is_lock']  = 0;
        $info = self::where($condition)->find();
        if($info){
            $result = self::updateLogin($info->getAttr('id'),$param['login_password']);
            return isset($result) ? $info : false;
        }
        return false;
    }     

    /**
     * 验证安全密码
     * @param  int $user_id 用户ID
     * @param  string $safepassword 验证的安全密码
     */
    public static function checkSafePasspord(int $uid,string $safepassword){
        $info = self::where(['id' => $uid])->find();
        if(password_verify(md5($safepassword),$info->getAttr('safe_password'))) {
            return true;
        }
        return false;
    } 

    /**
     * 验证登录密码
     * @param  int $user_id 用户ID
     * @param  string $password 验证的安全密码
     */
    public static function checkPasspord(int $uid,string $password){
        $info = self::where(['id' => $uid])->find();
        if(password_verify(md5($password),$info->getAttr('password'))) {
            return true;
        }
        return false;
    } 

    /**
     * 更新安全密码
     * @param  array $param 更新的用户信息
     */
    public static function updateSafePasspord(int $uid,string $safepassword){
        $data['id']            = $uid;
        $data['safe_password'] = password_hash(md5($safepassword),PASSWORD_DEFAULT);
        return self::update($data);
    } 
    
    /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected static function updateLogin($uid,$passpord = null){
        $data = [
            'id'         => $uid,
            'login_time' => time(),
            'login_ip'   => request()->ip()
        ];
        if(!empty($passpord)){
            $data['password'] = password_hash(md5($passpord),PASSWORD_DEFAULT);
        }
        return self::update($data);
    }

    /**
     * 锁定用户
     * @param integer $id
     */
    public static function lock(int $id){
        $result = self::where(['id' => $id])->find();
        $result->is_lock = $result->is_lock ? 0 : 1;
        if($result->is_lock){
            SystemMemberMiniapp::where(['member_id' => $id])->update(['is_lock' => 1]);
        }
        return $result->save();
    } 
}