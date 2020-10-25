<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户管理 Table<ai_member_miniapp>
 */
namespace app\common\model;
use think\Model;
use app\common\model\Member;
use util\Util;

class MemberMiniapp extends Model{

    protected $pk = 'id';

    /**
     * 关联应用
     *
     * @return void
     */
    public function miniapp(){
        return $this->hasOne('Miniapp','id','miniapp_id');
    }

    /**
    * 应用后台所属管理员
    * @return void
    */
    public function member(){
        return $this->hasOne('Member','id','member_id');
    }

    /**
    * 应用绑定的用户端口创始人
    * @return void
    */
    public function user(){
        return $this->hasOne('User','id','uid');
    }
    
    /**
    * 用户购买的应用
    * @return void
    */
    public function order(){
        return $this->hasOne('MemberMiniappOrder','id','miniapp_order_id');
    }
       
    /**
     * 用户小程序列表
     */
    public static function lists(array $param = [],$pages = []){
        return self::view('member_miniapp','*')
        ->view('member','phone_id','member.id = member_miniapp.member_id')
        ->view('miniapp','types,miniapp_dir,is_wechat_pay,is_alipay_pay,is_lock as miniapp_lack,is_openapp,template_id','miniapp.id = member_miniapp.miniapp_id')
        ->view('member_miniapp_order', 'update_var', 'member_miniapp.miniapp_order_id = member_miniapp_order.id')
        ->where($param)
        ->where(['miniapp.is_lock' => 0])
        ->order('id desc')
        ->paginate(10,false,['query' => $pages]);
    } 
    
    /**
     * 后台添加编辑
     * @param  array $param 数组
     */
    public static function edit(array $param){
        $data['member_id']   = $param['member_id'];
        $data['appname']     = $param['appname'];
        $data['update_time'] = time();
        if(isset($param['id']) && $param['id'] > 0){
            return self::where('id',$param['id'])->update($data);
        }else{
            $data['miniapp_id']        = $param['miniapp_id'];
            $data['miniapp_order_id']  = $param['miniapp_order_id'];
            $data['create_time']       = time();
            $last_id = self::insertGetId($data);
            return self::where('id',$last_id)->update(['service_id' => uuid(3,true,$last_id)]);
        }
    }

    /**
     * 用户添加编辑
     * @param  array $param 数组
     */
    public static function editer(array $param){
        $data = Util::array_remove_empty($param);
        $data['update_time'] = time();
        return self::where('id',$param['id'])->update($data);
    } 

    /**
     * 锁定用户
     * @param integer $id
     */
    public static function lock(int $id){
        $result = self::where(['id' => $id])->find();
        $result->is_lock = $result->is_lock ? 0 : 1;
        if($result->is_lock == 0){
            $member = Member::where(['id' => $result->member_id])->field('is_lock')->find();
            if($member->is_lock == 1){
                return false;
            }
        }
        return $result->save();
    } 
}