<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 运费管理
 */
namespace app\fastshop\model;
use think\Model;

class Fare extends Model{
    
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_fare';
    protected $autoWriteTimestamp = true;
    protected $createTime = false;

    /**
     * 计算运费多少钱
     * @param  array   $item [计算参数]
     * @return array         商品价格信息
     */
    public static function realAmount($item,$member_miniapp_id){
        $fare  = self::where(['member_miniapp_id' => $member_miniapp_id])->find();
        $real_amount  = 0;   //商品总价
        $real_weight  = 0;   //商品总重量
        $real_freight = 0;   //运费总价
        //计算商品价格+商品总重量
        foreach($item as $value){
            $real_amount += $value['amount'];
            $real_weight += $value['weight'] * $value['num'];
        }
        //计算运费和商品价格
        if($real_weight <= $fare['first_weight'] || 0 == $fare['second_weight']){
            $real_freight  = $fare['first_price'];
        }else{
            $weight = $real_weight - $fare['second_weight'];
            $real_freight  = $fare['first_price'] + ceil($weight/$fare['second_weight']) * $fare['second_price'];
        }
        $data['real_amount']  = money($real_amount);   //商品价格
        $data['real_freight'] = money($real_freight);  //运费
        $data['order_amount'] = money($real_freight+$real_amount);  //商品总价+运费
        return $data;
    }
}