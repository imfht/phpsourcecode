<?php

/**
 * 操作订单数据的 trait
 * Created by PhpStorm.
 * User: root
 * Date: 7/1/16
 * Time: 4:49 PM
 */
trait OrderUtil
{
    /* 计算折扣 */
    protected function compute_discount(){
        //TODO 没看懂　　后面再说
        return 0;
    }
    /**
     * 计算折扣后的总价格
     * @return int
     */
    protected function compute_discount_amount(){
        //TODO 后面实现
        return 0;
    }
    /**
     * 将积分换算成人民币
     * @param $integral
     * @return float|int
     */
    protected function value_of_integral($integral){
        $scale=$this->config['integral_scale'];
        return $scale > 0 ? round(($integral / 100) * $scale, 2) : 0;
    }
    //获得上一次用户采用的支付和配送方式
    protected function last_shipping_and_payment(){
        $sql = "SELECT shipping_id, pay_id from ecs_order_info WHERE user_id={$this->user_id}  ORDER BY order_id DESC LIMIT 1";
        $row = $this->find($sql);
        if (empty($row))
        {
            /* 如果获得是一个空数组，则返回默认值 */
            $row = array('shipping_id' => 0, 'pay_id' => 0);
        }

        return $row;
    }

    /**
     * 获取用户增值税资质
     * @return mixed
     */
    protected function zizhi(){
        $sql="SELECT * FROM ecs_zengzhishui_zizhi WHERE user_id={$this->user_id}";
        $zizhi=$this->find($sql);
        return $zizhi;
    }
    /**
     * 获取购物车中商品的价格
     * @param $seller_id
     * @return int
     */
    protected function amount($seller_id){
        $sql="select goods_price,goods_number from ecs_cart WHERE seller_id={$seller_id} AND user_id={$this->user_id} AND is_pay='1'";
        $goods=$this->select($sql);
        $amount=0;
        foreach($goods as $good){
            $amount+=$good['goods_price']*$good['goods_number'];
        }
        return $amount;
    }
}