<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 代理管理
 */
namespace app\popupshop\model;
use think\Model;
use app\common\model\SystemUser;
use app\common\model\SystemUserLevel;

class Agent extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_agent';
  
    /**
     * 绑定的用户
     * @return void
     */
    public function User(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }

    /**
      * 选择代理用户类别
     */
    public static function selects($condition){
        $user = self::where(['member_miniapp_id' => $condition['member_miniapp_id']])->field('user_id')->select()->toArray();
        $user_id = [];
        if(!empty($user)){
            $user_id = array_column($user,'user_id');
        } 
        return SystemUser::where($condition)->whereNotIn('id',$user_id)->order('id desc')->paginate(10,false);
    }

    /**
      * 读取代理用户ID(API使用)
     */
    public static function agentUid(array $condition,$miniapp_id){
        $level = SystemUserLevel::where($condition)->field('parent_id,level,user_id')->select()->toArray();
        if(empty($level)){
            return;
        }
        $uid = array_column($level,'parent_id');
        $agent = self::where(['member_miniapp_id' => $miniapp_id])->whereIn('user_id',$uid)->select()->toArray();
        if(empty($agent)){
            return;
        }
        $agent_uid = [];
        foreach ($agent as $key => $value) {
            $agent_uid[$key]['user_id'] = $value['user_id'];
            $agent_uid[$key]['rebate']  = $value['rebate'];
        }
        rsort($agent_uid);
        return empty($agent_uid[0]) ? false : $agent_uid[0];
    }

    /**
     * 添加编辑
     * @param  array $param 数组
     */
    public static function add(int $miniapp_id,array $ids){
        $data = [];
        $user = self::where(['member_miniapp_id' => $miniapp_id,'user_id' => $ids])->field('user_id')->select()->toArray();
        $user_id = [];
        if(!empty($user)){
            $user_id = array_column($user,'user_id');
        }
        foreach ($ids as $key => $value) {
            if(!in_array($value,$user_id)){
                $data[$key]['member_miniapp_id'] = $miniapp_id;
                $data[$key]['user_id']           = $value;
                $data[$key]['rebate']            = 0;
            }
        }
        if(empty($data)){
            return;
        }
        return self::insertAll($data);
    }
}