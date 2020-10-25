<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\fastshop\model;
use think\Model;

class Shopping extends Model{
    
    protected $pk    = 'id';
    protected $table = 'ai_fastshop_shopping';
    protected $table_cache = 'fastshop_shopping_cache';  //订单商品缓存表
    protected $table_field = "A.id as order_id,A.user_id,A.is_del,A.status,A.order_no,A.real_freight,A.real_amount,A.order_endtime,A.order_starttime,A.order_amount,A.express_status,A.paid_at,A.paid_time,A.express_starttime,A.express_no,A.express_company,A.express_name,A.express_phone,A.express_address,B.*";
 
    //订单信息
    public function orderItem(){
        return $this->hasOne('ShoppingCache','order_no','order_no');
    }


    //订单列表
    public function orderList(){
        return $this->hasMany('ShoppingCache','order_no','order_no');
    }
    
    /**
     * 获取订单数据(单个订单预览)
     * 使用：前台查询用户自己的订单
     * @param string $order_no 订单ID
     * @param integer $user_id 要获取的订单用户
     * @param integer $type  要获取的订单状态
     * @return array
     */
    public function getOrder(string $order_no,int $user_id = 0,int $is_del = 1){
        if($user_id){
            $condition['user_id']  = $user_id;
        }
        if($is_del){
            $condition['is_del']    = 0;
        }
        $condition['B.order_no'] = $order_no;
        $order = self::alias('A')->join($this->table_cache.' B','A.order_no = B.order_no','LEFT')->field($this->table_field)->where($condition)->order('id desc')->select()->toArray();
        return self::order_data($order);
    } 

    /**
     * 获取用户订单数据(v1.0待清理)（用户订单管理）
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
        $order = self::where($condition)->order('id desc')->paginate(10)->toArray();
        return self::order_data($order['data']);
    } 

    /**
     * 根据条件查询订单（后台订单管理）
     * @param array $order_no
     * @return void
     */
    public function getOrderList(array $condition,$status = 0,int $page_number = 10){
        return self::view('fastshop_shopping','id as order_id,user_id,is_del,status,order_no,real_freight,real_amount,order_endtime,order_starttime,order_amount,express_status,paid_at,paid_time,express_starttime,express_no,express_company,express_name,express_phone,express_address')
                ->view('fastshop_shopping_cache','*', 'fastshop_shopping.order_no = fastshop_shopping_cache.order_no','LEFT')
                ->where($condition)
                ->order('id desc')->paginate($page_number,false,['query'=>['status' => $status]]);
    }

    /**
     * 无翻页
     */
    public function getOrderListNopage(array $condition,$status = 0){
        return self::view('fastshop_shopping','id as order_id,user_id,is_del,status,order_no,real_freight,real_amount,order_endtime,order_starttime,order_amount,express_status,paid_at,paid_time,express_starttime,express_no,express_company,express_name,express_phone,express_address')
                ->view('fastshop_shopping_cache','*', 'fastshop_shopping.order_no = fastshop_shopping_cache.order_no','LEFT')
                ->where($condition)
                ->order('id desc')->select();
    }

   /**
     * 订单数据处理(v1.0待清理)
     * @param array $data
     * @return array
     */
    public function order_data(array $data){  
        $item = [];
        foreach ($data as $value) {
            $sku['name']               = $value['name'];
            $sku['img']                = $value['img'];
            $sku['item_id']            = $value['item_id'];
            $sku['buy_price']          = money($value['buy_price']);
            $sku['buy_nums']           = $value['buy_nums'];
            $sku['amount']             = money($value['buy_price']*$value['buy_nums']);
            $item[$value['order_no']]['order_no']          = (string)$value['order_no'];
            $item[$value['order_no']]['id']                = $value['order_id'];
            $item[$value['order_no']]['user_id']           = $value['user_id'];
            $item[$value['order_no']]['status']            = $value['status'];
            $item[$value['order_no']]['status_text']       = self::status($value['status'],$value['paid_at'],$value['express_status']);
            $item[$value['order_no']]['is_del']            = $value['is_del'];
            $item[$value['order_no']]['real_freight']      = money($value['real_freight']);
            $item[$value['order_no']]['real_amount']       = money($value['real_amount']);
            $item[$value['order_no']]['order_amount']      = money($value['order_amount']);
            $item[$value['order_no']]['order_starttime']   = date('Y-m-d H:s',$value['order_starttime']);
            $item[$value['order_no']]['order_endtime']     = date('Y-m-d H:s',$value['order_endtime']);
            $item[$value['order_no']]['paid_at']           = $value['paid_at'];
            $item[$value['order_no']]['paid_time']         =  empty($value['paid_time']) ? '' : date('Y-m-d H:s',$value['paid_time']);
            $item[$value['order_no']]['express_status']    = $value['express_status'];
            $item[$value['order_no']]['express_no']        = $value['express_no'];
            $item[$value['order_no']]['express_starttime'] = empty($value['express_starttime']) ? '' : date('Y-m-d H:s',$value['express_starttime']);
            $item[$value['order_no']]['express_company']   = $value['express_company'];
            $item[$value['order_no']]['express_name']      = $value['express_name'];
            $item[$value['order_no']]['express_phone']     = $value['express_phone'];
            $item[$value['order_no']]['express_address']   = $value['express_address'];
            $item[$value['order_no']]['item'][]            = $sku;
        }
        return $item;
    }
   
    /**
     * 状态数字变文字(v1.0待清理)
     * @return void
     */
    protected function status($status,$paid_at,$express_status){
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
    
    /**
     * 订单数据处理 v3.0
     * @param array $data
     * @return array
     */
    public static function orderData($data){
        $item = [];
        foreach ($data as $keys => $value) {
            $sku = [];
            foreach ($value->OrderList  as $key => $rs) {
                $sku[$key]['name']      = $rs['name'];
                $sku[$key]['img']       = $rs->img;
                $sku[$key]['item_id']   = $rs->item_id;
                $sku[$key]['buy_price'] = $rs->buy_price;
                $sku[$key]['buy_nums']  = $rs->buy_nums;
                $sku[$key]['amount']    = money($rs->buy_price*$rs->buy_nums);
            }
            $item[$keys]['item']              = $sku;
            $item[$keys]['id']                = $value->id;
            $item[$keys]['order_no']          = $value->order_no;
            $item[$keys]['user_id']           = $value->user_id;
            $item[$keys]['status']            = $value->status;
            $item[$keys]['status_text']       = self::statusText($value->status,$value->paid_at,$value->express_status);
            $item[$keys]['is_del']            = $value->is_del;
            $item[$keys]['real_freight']      = money($value->real_freight);
            $item[$keys]['real_amount']       = money($value->real_amount);
            $item[$keys]['order_amount']      = money($value->order_amount);
            $item[$keys]['order_starttime']   = date('Y-m-d H:s',$value->order_starttime);
            $item[$keys]['order_endtime']     = date('Y-m-d H:s',$value->order_endtime);
            $item[$keys]['paid_at']           = $value->paid_at;
            $item[$keys]['paid_time']         = empty($value->paid_time) ? '' : date('Y-m-d H:s',$value->paid_time);
            $item[$keys]['express_status']    = $value->express_status;
            $item[$keys]['express_no']        = $value->express_no;
            $item[$keys]['express_starttime'] = empty($value->express_starttime) ? '' : date('Y-m-d H:s',$value['express_starttime']);
            $item[$keys]['express_company']   = $value->express_company;
            $item[$keys]['express_name']      = $value->express_name;
            $item[$keys]['express_phone']     = $value->express_phone;
            $item[$keys]['express_address']   = $value->express_address;
        }
        return $item;
    }

    /**
     * 状态数字变文字 v3.0
     * @return void
     */
    protected static function statusText($status,$paid_at,$express_status){
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