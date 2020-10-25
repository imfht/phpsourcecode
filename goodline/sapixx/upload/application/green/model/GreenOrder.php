<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\green\model;
use think\Model;

class GreenOrder extends Model{
    

    //所属商品
    public function shop(){
        return $this->hasOne('GreenShop','shop_id','id');
    }

    /**
     * @param $data
     * @param $order_no
     * 购买商品生成订单数据
     */
    public static function insertOrder($param,$order_no){
        $order = [
            'shop_id'           => $param['shop_id'],
            'member_miniapp_id' => $param['member_miniapp_id'],
            'message'           => $param['message'],
            'user_id'           => $param['user_id'],
            'order_no'          => $order_no,
            'points'            => $param['points'],
            'shop_cache'        => $param['shop_cache'],
            'express_name'      => $param['express_name'],
            'express_phone'     => $param['express_phone'],
            'express_address'   => $param['express_address'],
            'status'            => 0,
            'is_del'            => 0,
            'paid_at'           => 0,
            'create_time'       => time()
        ];
        return self::create($order);
    }

    /**
     * 获取订单数据(单个订单预览)
     * 使用：前台查询用户自己的订单
     * @param string $order_no 订单ID
     * @param integer $user_id 要获取的订单用户
     * @param integer $type  要获取的订单状态
     * @return array
     */
    public static function getOrder(string $order_no,int $user_id = 0,int $is_del = 1){
        if($user_id){
            $condition['user_id']  = $user_id;
        }
        if($is_del){
            $condition['is_del']    = 0;
        }
        $condition['order_no'] = $order_no;
        return self::where($condition)->order('id desc')->select();
    } 
}