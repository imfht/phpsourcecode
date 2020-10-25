<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\fastshop\model;
use think\Model;
use app\fastshop\model\Item;

class Order extends Model{
    
    protected $pk    = 'id';
    protected $table = 'ai_fastshop_order';
    protected $table_cache = 'fastshop_order_cache';  //订单商品缓存表
    protected $table_field = "A.id as order_id,A.is_point,A.is_entrust,A.user_id,A.is_del,A.is_fusion,A.status,A.order_no,A.real_freight,A.real_amount,A.order_endtime,A.order_starttime,A.order_amount,A.express_status,A.paid_at,A.paid_time,A.express_starttime,A.express_no,A.express_company,A.express_name,A.express_phone,A.express_address,B.*";

    
    //委托列表
    public function entrust(){
        return $this->hasOne('EntrustList','order_no','order_no');
    }

    //和活动主表管理的
    public function sale(){
        return $this->hasOne('Sale','id','sale_id');
    }

    //订单列表
    public function orderItem(){
        return $this->hasOne('OrderCache','order_no','order_no');
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
     * 获取用户订单数据（用户订单管理）
     * @param integer $user_id  要获取的订单用户
     * @param integer $type  要获取的订单状态
     * @return array
     */
    public function getUserOrderList(int $user_id,int $type = 0){
        $condition['user_id'] = $user_id;
        $condition['is_del']  = 0;
        switch ($type) {
            case 1:
                $condition['paid_at']    = 1;
                $condition['is_entrust'] = 1;
                break;
            case 2:
                $condition['paid_at'] = 1;
                $condition['express_status'] = 1;
                $condition['status'] = 0;
                break;
            case 3:
                $condition['status'] = 1;
                break;
            default:
                $condition['paid_at']    = 1;
                break;
        }
        $order = self::alias('A')->join($this->table_cache.' B','A.order_no = B.order_no','LEFT')->field($this->table_field)->where($condition)->order('id desc')->paginate(10)->toArray();
        return self::order_data($order['data']);
    } 

    /**
     * 根据条件查询订单（后台订单管理）
     * @param array $order_no
     * @return void
     */
    public function getOrderList(array $condition,$status = 0,int $page_number = 10){
        return self::view('fastshop_order','id as order_id,is_point,is_entrust,is_fusion,user_id,is_del,status,order_no,real_freight,real_amount,order_endtime,order_starttime,order_amount,express_status,paid_at,paid_time,express_starttime,express_no,express_company,express_name,express_phone,express_address')
                ->view('fastshop_order_cache','*','fastshop_order.order_no = fastshop_order_cache.order_no')
                ->where($condition)
                ->order('id desc')->paginate($page_number,false,['query'=>['status' => $status]]);
    }
   /**
     * 无翻页
     */
    public function getOrderListNopage(array $condition,$status = 0){
        return self::view('fastshop_order','id as order_id,is_point,is_entrust,is_fusion,user_id,is_del,status,order_no,real_freight,real_amount,order_endtime,order_starttime,order_amount,express_status,paid_at,paid_time,express_starttime,express_no,express_company,express_name,express_phone,express_address')
                ->view('fastshop_order_cache','*','fastshop_order.order_no = fastshop_order_cache.order_no')
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
        foreach ($data as $key => $value) {
            $sku['name']               = $value['name'];
            $sku['img']                = $value['img'];
            $sku['item_id']            = $value['item_id'];
            $sku['sale_price']         = money($value['sale_price']/100);
            $sku['amount']             = $sku['sale_price'];
            $item[$value['order_no']]['order_no']          = (string)$value['order_no'];
            $item[$value['order_no']]['id']                = $value['order_id'];
            $item[$value['order_no']]['user_id']           = $value['user_id'];
            $item[$value['order_no']]['is_entrust']        = $value['is_entrust'];
            $item[$value['order_no']]['is_point']          = $value['is_point'];
            $item[$value['order_no']]['is_fusion']         = $value['is_fusion'];
            $item[$value['order_no']]['status']            = $value['status'];
            $item[$value['order_no']]['status_text']       = self::status($value['status'],$value['paid_at'],$value['is_entrust'],$value['express_status']);
            $item[$value['order_no']]['is_del']            = $value['is_del'];
            $item[$value['order_no']]['real_freight']      = money($value['real_freight']);
            $item[$value['order_no']]['real_amount']       = money($value['real_amount']);
            $item[$value['order_no']]['order_amount']      = money($value['order_amount']);
            $item[$value['order_no']]['order_starttime']   = empty($value['order_starttime']) ? '' : date('Y-m-d H:i:s',$value['order_starttime']);
            $item[$value['order_no']]['order_endtime']     = empty($value['order_endtime']) ? '' : date('Y-m-d H:i',$value['order_endtime']);
            $item[$value['order_no']]['paid_at']           = $value['paid_at'];
            $item[$value['order_no']]['paid_time']         = date('Y-m-d H:i:s',$value['paid_time']);
            $item[$value['order_no']]['express_status']    = $value['express_status'];
            $item[$value['order_no']]['express_no']        = $value['express_no'];
            $item[$value['order_no']]['express_starttime'] = empty($value['express_starttime']) ? '' : date('Y-m-d H:i:s',$value['express_starttime']);
            $item[$value['order_no']]['express_company']   = $value['express_company'];
            $item[$value['order_no']]['express_name']      = $value['express_name'];
            $item[$value['order_no']]['express_phone']     = $value['express_phone'];
            $item[$value['order_no']]['express_address']   = $value['express_address'];
            $item[$value['order_no']]['item']              = $sku;
            $item[$value['order_no']]['gift']              = widget('fastshop/order/gift',['gift' => json_decode($value['gift'],true)]);
            $item[$value['order_no']]['entrust']           = json_decode($value['entrust'],true);
            $item[$value['order_no']]['fusion_state']      = $value['fusion_state'];
        }
        return $item;
    }
   

    /**
     * 状态数字变文字(前台待清理)
     * @return void
     */
    protected function status($status,$paid_at,$is_entrust,$express_status){
        if($status == 0){
            if($paid_at == 1){
                if($is_entrust == 0){
                    $status_text = '待确认';
                }else{
                    if($express_status == 0){
                        $status_text = '待收货';
                    }else{
                        $status_text = '已发货';
                    }
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
     * 状态数字变文字(前台)
     * @return void
     */
    public static function statuText($status,$paid_at,$is_entrust,$express_status){
        if($status == 0){
            if($paid_at == 1){
                if($is_entrust == 0){
                    $status_text = '待确认';
                }else{
                    if($express_status == 0){
                        $status_text = '待收货';
                    }else{
                        $status_text = '已发货';
                    }
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
     * 读取订单的赠品价格和所属产品图片数据
     */
    public static function gift(array $gift){
        $gift_id = array_column($gift,'item_id');
        $list = Item::field('id,name,img,imgs,content,weight')->whereIn('id',$gift_id)->select()->toArray();
        $gift_data = [];
        foreach ($gift as $k => $v) {
            $gift_value['item_id']       = $v['item_id'];
            $gift_value['sale_price']    = money($v['sale_price']/100);
            $gift_value['market_price']  = money($v['market_price']/100);
            foreach ($list as $value) {
                if ($v['item_id'] == $value['id']) {
                    $gift_data[$k] = array_merge($value,$gift_value);
                    $gift_data[$k]['img']     = $value['img']."?x-oss-process = style/auto";
                    $gift_data[$k]['imgs']    = json_decode($value['imgs'],true);
                    $gift_data[$k]['weight']  = $value['weight'];
                    $gift_data[$k]['content'] = $value['content'];
                }
            }
        }
        return $gift_data; 
    }
}