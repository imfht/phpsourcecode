<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 
 * 用户管理 Table<ai_member_miniapp_order>
 */
namespace app\common\model;
use think\Model;

class MemberMiniappOrder extends Model{

    protected $pk = 'id';

    /**
     * 用户小程序列表
     */
    public function lists(array $param){
        return self::view('member_miniapp_order','id as order_id,start_time,end_time,is_lock as is_order_lock')
            ->view('miniapp','*','member_miniapp_order.miniapp_id = miniapp.id')
            ->where($param)
            ->order('id desc')
            ->paginate(20,true);
    } 
}