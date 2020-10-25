<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户表 Table<ai_user>
 */
namespace app\common\model;
use think\Model;

class User extends Model{
    
    protected $pk = 'id';

    /**
     * 判断是否邀请用户
     * @param integer $user_id 当前用户ID
     * @param string  $code    邀请码
     * @return int    邀请用户ID
     */
    public static function isInvite($invite_code){
        if(empty($invite_code)){
            return;
        }
        $id = de_code(strtoupper($invite_code));
        $is_invite = self::where(['id' => $id])->field('id')->find();
        return empty($is_invite) ? 0 : $is_invite['id'];
    }

    /**
     * 通过微信注册或更新
     * @param array $wechat
     * @param int $is_miniapp 0小程序  1公众号
     * @return 成功后返回用户的ID
     */
    public static function wechatReg(array $wechat,$is_miniapp = true){
        //参数
        $data   = [];
        $updata = [];
        $data['member_miniapp_id'] = $wechat['miniapp_id'];
        $data['nickname']          = $wechat['nickname'];
        $data['face']              = $wechat['avatar'];
        $data['official_uid']      = $wechat['official_uid'];
        $data['miniapp_uid']       = $wechat['miniapp_uid'];
        $data['wechat_uid']        = $wechat['wechat_uid'];
        $data['login_time']        = time();
        $data['update_time']       = time();
        $data['login_ip']          = request()->ip();
        //查询用户类型
        $condition['member_miniapp_id']  = $wechat['miniapp_id'];
        if($is_miniapp){
            $data['session_key']       = $wechat['session_key'];
            $updata['session_key']     = $wechat['session_key'];
            $condition['miniapp_uid']  = $data['miniapp_uid'];
        }else{
            $condition['official_uid'] = $data['official_uid'];
        }
        $info = self::where($condition)->find();
        if(empty($info)){
            $data['create_time'] = time();
            $is_insert = true;
            //如果用户先进入公众号,后进入小程序保证账户同步
            if($is_miniapp && !empty($data['official_uid'])){ 
                $official_info = self::where(['member_miniapp_id' => $wechat['miniapp_id'],'official_uid' => $data['official_uid']])->find();
                if($official_info){
                    $is_insert = false;  
                    $last_id   = $official_info['id']; 
                    self::update(['id' => $last_id,'miniapp_uid' => $data['miniapp_uid']]);             
                }
            }
            //判断是否需要增加
            if($is_insert){
                $last_id = self::insertGetId($data);
                if($last_id){
                    self::where('id',$last_id)->data(['invite_code' => create_code($last_id)])->update();
                }
                //添加用户邀请来源
                if(!empty($wechat['invite_code'])){
                    $is_invite = model('User')->isInvite($wechat['invite_code']);  //临时解决邀请问题
                    if($is_invite){
                        model('UserLevel')->addLevel($last_id,$is_invite);
                    }
                }
            }
        }else{
            $updata['nickname']     = $wechat['nickname'];
            $updata['face']         = $wechat['avatar'];
            $updata['login_time']   = time();
            $updata['login_ip']     = request()->ip();
            $updata['session_key'] = $wechat['session_key'];
            if($is_miniapp && !empty($data['official_uid'])){
                $updata['official_uid'] = $data['official_uid'];
            }
            self::where(['id' => $info->id])->data($updata)->update();  
            $last_id = $info->id;
        }
        return $last_id;
    }

    /**
     * 更新安全密码
     * @param  array $param 更新的用户信息
     */
    public static function updateSafePasspord(int $uid,string $safepassword){
        $data['safe_password'] = password_hash(md5($safepassword),PASSWORD_DEFAULT);
        return User::where(['id' => $uid])->update($data);
    } 

    /**
     * 修改登录密码
     * @access public
     */
    public function upDatePasspowrd(int $uid,string $password){
        $data['password'] = password_hash(md5($password),PASSWORD_DEFAULT);
        return User::where(['id' => $uid])->update($data);
    } 

    /**
     * 锁定用户
     * @param integer $id
     */
    public static function lock(int $appid,int $id){
        $result = self::where(['member_miniapp_id' => $appid,'id' => $id])->find();
        if($result->is_delete >= 1){
            return FALSE;
        }
        $result->is_lock = $result->is_lock ? 0 : 1;
        return $result->save();
    }

    /**
     * 登录用户ID
     */
    public static function edit(array $data,int $id){
        return User::where(['id' => $id])->update($data);
    }

    /**
     * 作废
     * @param integer $id
     */
    public static function isDelete(int $appid,int $id){
        $result = self::where(['member_miniapp_id' => $appid,'id' => $id])->find();
        if($result->is_delete >= 1){
            return FALSE;
        }
        $result->is_lock      = 1;
        $result->is_delete    = 1;
        $result->phone_uid    = '';
        $result->wechat_uid   = '';
        $result->official_uid = '';
        $result->miniapp_uid  = '';
        return $result->save();
    }
}