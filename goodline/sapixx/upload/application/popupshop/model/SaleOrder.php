<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\popupshop\model;
use think\Model;

class SaleOrder extends Model{
    
    protected $pk    = 'id';
    protected $table = 'ai_popupshop_sales_order';

    //订单列表
    public function orderItem(){
        return $this->hasOne('SaleOrderCache','sale_order_id','id');
    }

    //所属套餐
    public function sale(){
        return $this->hasOne('Sale','id','sale_id');
    }  

    //订单列表
    public function orderList(){
        return $this->hasMany('SaleOrderCache','sale_order_id','id');
    }

    /**
     * 订单数据处理
     * @param array $data
     * @return array
     */
    public static function order_data($param){
        $item = [];
        $sku = [];
        foreach ($param->orderList  as $key => $rs) {
            $sku[$key]['id']         = $rs->id;
            $sku[$key]['house_id']   = $rs->house_id;
            $sku[$key]['name']       = $rs['name'];
            $sku[$key]['img']        = $rs->img;
            $sku[$key]['sale_price'] = $rs->sale_price;
            $sku[$key]['is_sales']   = $rs->is_sales;
            $sku[$key]['is_entrust'] = $rs->is_entrust;
            $sku[$key]['is_out']     = $rs->is_out;
            $sku[$key]['note']       = $rs->house->note;
        }
        $item['id']                = $param->id;
        $item['order_no']          = $param->order_no;
        $item['user_id']           = $param->user_id;
        $item['status']            = $param->status;
        $item['is_entrust']        = $param->is_entrust;
        $item['status_text']       = self::status($param);
        $item['is_del']            = $param->is_del;
        $item['is_out']            = $param->is_out;
        $item['is_settle']         = $param->is_settle;
        $item['real_freight']      = $param->real_freight;
        $item['real_amount']       = $param->real_amount;
        $item['order_amount']      = $param->order_amount;
        $item['order_starttime']   = date('Y-m-d H:s',$param->order_starttime);
        $item['order_endtime']     = empty($param->order_endtime) ? '' : date('Y-m-d H:s',$param->order_endtime);
        $item['paid_at']           = $param->paid_at;
        $item['paid_time']         = empty($param->paid_time) ? '' : date('Y-m-d H:s',$param->paid_time);
        $item['express_status']    = $param->express_status;
        $item['express_no']        = $param->express_no;
        $item['express_starttime'] = empty($param->express_starttime) ? '' : date('Y-m-d H:s',$param['express_starttime']);
        $item['express_company']   = $param->express_company;
        $item['express_name']      = $param->express_name;
        $item['express_phone']     = $param->express_phone;
        $item['express_address']   = $param->express_address;
        $item['orderList']         = $sku;
        return $item;
    }
    
    /**
     * 状态数字变文字(前台)
     * @return void
     */
    protected static function status($param){
        if($param->is_out == 0){
            if($param->status == 0){
                if($param->paid_at == 1){
                    if ($param->is_entrust == 1) {
                        if($param->express_status == 1){
                            $status_text = '已发货';
                        }else{
                            $status_text = '待发货';
                        }
                    }else{
                        $status_text = '待确认';
                    }
                }else{
                    $status_text = '待付款';
                }
            }else{
                $status_text = '订单结束';
            }
        }else{
            $status_text = '已退货';
        }
        return $status_text;
    }
}