<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单逻辑
 */
namespace app\fastshop\widget;
use think\facade\Cookie;

class Shopping{

    /**
     * 清空购物车和订单Cookie
     * @return void
     */
    public function clearCart(int $uid){
        return model('fastshop/Shopping')->table_cart()->where(['user_id' => $uid])->update(['cart' =>'[]']);
    }

     /**
     * 读取购物车中IDS的商品信息
     * @param [array] $cart  购物车中的数据 ['sku_id' => num]
     * @return void
     */
    public function cartItem(array $cart){
        $ids = ids(array_keys(array_filter($cart)));    //过滤查询ID
        $result = model('Item')->where(['is_sale' => 2])->whereIn('id',$ids)
                ->field('id,name,img,points,repoints,market_price,sell_price,cost_price,weight')
                ->select()->toArray();
        $item = [];
        foreach ($result as $key => $value) {
            $num = abs(intval($cart[$value['id']]));
            $num = $num <= 0 ? 1: $num;
            $sell_total = money($value['sell_price'] * $num);  //单个商品价格
            //最终价格(后期开发,加上商品活动的时候计算)
            $amount = $sell_total; //计算付款金额的字段
            $item[$value['id']] = [
                'id'           => $value['id'],
                'market_price' => money($value['market_price']),
                'sell_price'   => money($value['sell_price']),
                'weight'       => $value['weight'],
                'num'          => $num,
                'amount'       => $amount,
                'sell_total'   => $sell_total,
                'name'         => $value['name'],
                'img'          => $value['img'],
                'points'       => empty($value['points']) ? 0 : $value['points'],
                'repoints'     => empty($value['repoints']) ? 0 : $value['repoints'],
            ];
        }
        return $item;
    }
    
    /**
     * 保存订单
     * @param  array   $item [计算参数]
     * @return array         商品价格信息
     */
    public function saveOrder(array $data){
        $order['order_no']          = 'S'.order_no();
        $order['user_id']           = $data['user_id'];
        $order['member_miniapp_id'] = $data['member_miniapp_id'];
        $order['payment_id']        = $data['payment_id'];
        $order['real_amount']       = $data['real_amount'];
        $order['real_freight']      = $data['real_freight'];
        $order['order_amount']      = $data['order_amount'];
        $order['express_name']      = $data['express_name'];
        $order['express_phone']     = $data['express_phone'];
        $order['express_address']   = $data['express_address'];
        $order['paid_at']           = $data['paid_at'];
        if($data['paid_at'] == 1){
            $order['paid_time']     = time();
            $order['paid_no']       = $order['order_no'];
        }
        $order['status']            = 0;
        $order['is_del']            = 0;
        $order['express_status']    = 0;
        $order['order_starttime']   = time();
        $rel = model('fastshop/Shopping')->insertGetId($order);
        return empty($rel) ? false : $order['order_no'];
    }

    /**
     * 计算运费多少钱
     * @param  array   $item [计算参数]
     * @return array         商品价格信息
     */
    public function realAmount(array $item,$miniapp_id){
        $fare  = model('fastshop/fare')->get(['member_miniapp_id' => $miniapp_id]);
        $real_amount  = 0;   //商品总价
        $real_freight = 0;   //运费总价
        $total        = 0;   //单SKU运费
        foreach($item as $value){
            $real_amount += $value['amount'];
            $weight      = $value['weight'] * $value['num'];
            if($weight <= $fare['first_weight'] || 0 == $fare['second_weight']){
                $total  = $fare['first_price'];
            }else{
                $weight = $weight - $fare['second_weight'];
                $total  = $fare['first_price'] + ceil($weight/$fare['second_weight']) * $fare['second_price'];
            }
            $real_freight += $total;
        }
        $data['real_amount']  = money($real_amount);   //商品价格
        $data['real_freight'] = money($real_freight);  //运费
        $data['order_amount'] = money($real_freight+$real_amount);  //商品总价+运费
        return $data;
    }
}