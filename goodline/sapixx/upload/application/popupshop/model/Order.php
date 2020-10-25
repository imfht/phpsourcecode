<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\popupshop\model;
use think\Model;

class Order extends Model{
    
    protected $pk    = 'id';
    protected $table = 'ai_popupshop_order';

    //订单列表
    public function orderItem(){
        return $this->hasOne('OrderCache','order_id','id');
    }

    //订单列表
    public function orderList(){
        return $this->hasMany('OrderCache','order_id','id');
    }

    /**
     * 获取用户订单数据（用户订单管理）
     * @param integer $user_id  要获取的订单用户
     * @param integer $type  要获取的订单状态
     * @return array
     */
    public static function getUserOrderList(int $user_id,int $type = 0){
        $condition['user_id'] = $user_id;
        $condition['is_del']  = 0;
        switch ($type) {
            case 1:
                $condition['paid_at'] = 1;
                $condition['express_status'] = 0;
                break;
            case 2:
                $condition['paid_at']        = 1;
                $condition['express_status'] = 1;
                $condition['status']         = 0;
                break;
            case 3:
                $condition['paid_at'] = 1;
                $condition['status']  = 1;
                break;
            default:
                $condition['paid_at'] = 0;
                break;
        }
        return self::where($condition)->order('id desc')->paginate(10);
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

    /**
     * 订单数据处理
     * @param array $data
     * @return array
     */
    public static function order_data($data){
        $item = [];
        foreach ($data as $value) {
            $sku = [];
            foreach ($value->OrderList  as $key => $rs) {
                $sku[$key]['name']      = $rs['name'];
                $sku[$key]['note']      = $rs->item->note ?? '';
                $sku[$key]['img']       = $rs->img;
                $sku[$key]['item_id']   = $rs->item_id;
                $sku[$key]['buy_price'] = $rs->buy_price;
                $sku[$key]['buy_nums']  = $rs->buy_nums;
                $sku[$key]['amount']    = money($rs->buy_price*$rs->buy_nums);
            }
            $item[$value->id]['item']              = $sku;
            $item[$value->id]['id']                = $value->id;
            $item[$value->id]['order_no']          = $value->order_no;
            $item[$value->id]['user_id']           = $value->user_id;
            $item[$value->id]['status']            = $value->status;
            $item[$value->id]['status_text']       = self::status($value->status,$value->paid_at,$value->express_status);
            $item[$value->id]['is_del']            = $value->is_del;
            $item[$value->id]['real_freight']      = money($value->real_freight);
            $item[$value->id]['real_amount']       = money($value->real_amount);
            $item[$value->id]['order_amount']      = money($value->order_amount);
            $item[$value->id]['order_starttime']   = date('Y-m-d H:s',$value->order_starttime);
            $item[$value->id]['order_endtime']     = date('Y-m-d H:s',$value->order_endtime);
            $item[$value->id]['paid_at']           = $value->paid_at;
            $item[$value->id]['paid_time']         = empty($value->paid_time) ? '' : date('Y-m-d H:s',$value->paid_time);
            $item[$value->id]['express_status']    = $value->express_status;
            $item[$value->id]['express_no']        = $value->express_no;
            $item[$value->id]['express_starttime'] = empty($value->express_starttime) ? '' : date('Y-m-d H:s',$value['express_starttime']);
            $item[$value->id]['express_company']   = $value->express_company;
            $item[$value->id]['express_name']      = $value->express_name;
            $item[$value->id]['express_phone']     = $value->express_phone;
            $item[$value->id]['express_address']   = $value->express_address;
        }
        return $item;
    }
    

    /**
     * 状态数字变文字(前台)
     * @return void
     */
    protected static function status($status,$paid_at,$express_status){
        if($status == 0){
            if($paid_at == 1){
                if($express_status == 0){
                    $status_text = '已付款';
                }else{
                    $status_text = '待收货';
                }
            }else{
                $status_text = '待付款';
            }
        }else{
            $status_text = '订单结束';
        }
        return $status_text;
    }
}