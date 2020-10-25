<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 *
 * 微信认证权限表 Table<ai_member_miniapp_token>
 */
namespace app\common\model;

use think\Model;

class MemberMiniappToken extends Model{
    
    protected $pk = 'id';

    /**
     * 添加编辑
     * @param  array $param 数组
     */
    public static function edit(array $param){
        $data['authorizer_access_token']  = $param['access_token'];
        $data['authorizer_refresh_token'] = $param['refresh_token'];
        $data['expires_in']               = $param['expires_in'];
        $data['update_time']              = time();
        $miniapp = self::where(['member_miniapp_id' => $param['member_miniapp_id'],'authorizer_appid' => $param['appid']])->find();
        if($miniapp){
            return self::where(['member_miniapp_id' => $param['member_miniapp_id'],'authorizer_appid' => $param['appid']])->update($data);
        }else {
            $data['member_miniapp_id'] = $param['member_miniapp_id'];
            $data['authorizer_appid']  = $param['appid'];
            return self::insert($data);
        }
    }

    /**
     * 获取AccessToken
     * @param  array $param 数组
     */
    public static function accessToken(int $id,string $appid){
        $where['member_miniapp_id'] = $id;
        $where['authorizer_appid']  = $appid;
        $assess = self::where($where)->find();
        if ($assess) {
            $expires_in = time()-$assess['update_time'];
            if ($expires_in >= 6600) {
                return false;
            }
            return ['access_token'=>$assess['authorizer_access_token'],'appid' => $appid];
        }
        return false;
    }
    
    /**
     * 获取refreshToken
     * @param  array $param 数组
     */
    public static function refreshToken(int $id,string $appid){
        $where['member_miniapp_id'] = $id;
        $where['authorizer_appid']  = $appid;
        $assess = self::where($where)->find();
        if ($assess) {
            return ['refreshToken' => $assess['authorizer_refresh_token'],'appid' => $appid];
        }
        return false;
    } 
}
