<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户表
 */
namespace app\common\model;
use think\Model;
use app\common\model\SystemUserLevel;

class SystemUser extends Model{
    
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
        //查询用户类型
        $condition['member_miniapp_id'] = $wechat['miniapp_id'];
        if($is_miniapp){
            $condition['miniapp_uid']   = $wechat['miniapp_uid'];
        }else{
            $condition['official_uid']  = $wechat['official_uid'];
        }
        $info = self::where($condition)->find();
        if(empty($info)){
            $data = [];
            $data['member_miniapp_id'] = $wechat['miniapp_id'];
            $data['nickname']          = $wechat['nickname'];
            $data['face']              = $wechat['avatar'];
            $data['official_uid']      = $wechat['official_uid'];
            $data['miniapp_uid']       = $wechat['miniapp_uid'] ?: '';
            $data['wechat_uid']        = $wechat['wechat_uid'] ?: '';
            $data['create_time']       = time();
            $data['login_time']        = time();
            $data['update_time']       = time();
            $data['login_ip']          = request()->ip();
            if($is_miniapp){
                $data['session_key']   = $wechat['session_key'];
            }
            //在小程序端进行公众号绑定操作
            if($is_miniapp && !empty($data['official_uid'])){ 
                $official_info = self::where(['member_miniapp_id' => $wechat['miniapp_id'],'official_uid' => $data['official_uid']])->find();
                if($official_info){
                    $official_info->miniapp_uid = $data['miniapp_uid'];
                    $official_info->session_key = $data['session_key'];
                    $official_info->save();
                    return $official_info->id;
                }
            }
            //创建
            $last_id = self::insertGetId($data);
            if($last_id){
                self::where('id',$last_id)->data(['invite_code' => create_code($last_id)])->update();
                if(!empty($wechat['invite_code'])){ //创建邀请欢喜
                    $is_invite = self::isInvite($wechat['invite_code']);
                    if($is_invite){
                        SystemUserLevel::addLevel($last_id,$is_invite);
                    }
                }
            }
            return $last_id;
        }else{
            $info->nickname     = $wechat['nickname'];
            $info->face         = $wechat['avatar'];
            $info->login_time   = time();
            $info->login_ip     = request()->ip();
            if(!empty($wechat['session_key'])){
                $info->session_key  = $wechat['session_key'];
            }
            if($is_miniapp && !empty($wechat['official_uid'])){
                $info->official_uid = $wechat['official_uid'];
            }
            $info->save();
            return $info->id;
        }
    }

    /**
     * 更新安全密码
     * @param  array $param 更新的用户信息
     */
    public static function updateSafePasspord(int $uid,string $safepassword){
        $data['safe_password'] = password_hash(md5($safepassword),PASSWORD_DEFAULT);
        return SystemUser::where(['id' => $uid])->update($data);
    } 

    /**
     * 修改登录密码
     * @access public
     */
    public function upDatePasspowrd(int $uid,string $password){
        $data['password'] = password_hash(md5($password),PASSWORD_DEFAULT);
        return SystemUser::where(['id' => $uid])->update($data);
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
        return SystemUser::where(['id' => $id])->update($data);
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