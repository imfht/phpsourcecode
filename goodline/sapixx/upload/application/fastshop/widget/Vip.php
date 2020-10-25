<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 开通会员结算
 */
namespace app\fastshop\widget;

class Vip{

    /**
     * 开通会员推荐人收益
     * @param integer $miniapp_id   来源小程序
     * @param integer $uid  用户ID
     * @param float $cash_fee  (分)
     * @param [type] $config  系统配置
     * @return void
     */
    public function level(int $miniapp_id,int $uid,int $cash_fee){    
        $level = model('SystemUserLevel')->where(['user_id' => $uid,'level'=>[1,2]])->select();
        $level1 = 0;
        $level2 = 0;
        foreach ($level as $key => $value) {
            if($value['level'] == 1){
                $level1 = $value['parent_id'];
            }
            if($value['level'] == 2){
                $level2 = $value['parent_id'];
            }
        }
        $config   = model('Config')->where(['member_miniapp_id' => $miniapp_id])->find(); //读取配置
        $shopping = $config['shopping']/100;  //购物金比例
        if($level1){//一级
            //查询是否会员
            $rel = model('Vip')->field('state')->where(['member_miniapp_id'=>$miniapp_id,'user_id' => $level1,'state'=>1])->count();
            if($rel){
                $level1_print = $cash_fee*($config['regvip_level1_ratio']/100);   //直推反比多少钱
                $small_shop = intval($level1_print*$shopping);         //购物
                $small_due  = intval($level1_print-$small_shop);       //剩下多少
                if($small_shop > 0 && $small_due > 0){
                    model('Bank')->due_up($miniapp_id,$level1,$small_due,$small_shop);
                    model('BankLogs')->add($miniapp_id,$level1,intval($level1_print),'会员贡献收益,积分'.money($level1_print/100).' 已结算');
                }
            }
        }
        if($level2){ //二级
            $rel = model('Vip')->field('state')->where(['member_miniapp_id'=>$miniapp_id,'user_id' => $level2,'state'=>1])->count();
            if($rel){
                $level2_print = $cash_fee*($config['regvip_level2_ratio']/100);   //直推反比多少钱
                $big_shop = intval($level2_print*$shopping);         //购物
                $big_due  = intval($level2_print-$big_shop);       //剩下多少
                if($big_shop > 0 && $big_due > 0){
                    model('Bank')->due_up($miniapp_id,$level2,$big_due,$big_shop);
                    model('BankLogs')->add($miniapp_id,$level2,intval($level2_print),'会员贡献收益 ,积分'.money($level2_print/100).' 已结算');
                }
            }
        }
    }
}