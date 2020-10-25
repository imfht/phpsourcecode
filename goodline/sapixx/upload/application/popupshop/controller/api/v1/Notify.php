<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\popupshop\controller\api\v1;
use app\popupshop\controller\api\Base;
use app\common\facade\WechatPay;
use app\popupshop\model\SaleOrder;
use app\popupshop\model\Config;
use app\popupshop\model\SaleUser;
use app\popupshop\model\Sale;
use app\popupshop\model\Bank;
use app\popupshop\model\BankBill;
use app\popupshop\model\BankRecharge;
use app\popupshop\model\Order;
use app\common\model\SystemMemberBankBill;
use app\common\model\SystemMemberBank;
use Exception;

class Notify extends Base{
 
    /**
     *  微信小程序支付回调
     * @return void
     */
    public function sale(){
        try {
            $response = WechatPay::doPay($this->miniapp_id)->handlePaidNotify(function($message,$fail){
                $result = SaleOrder::where(['order_no' => $message['out_trade_no'],'paid_at' => 0])->find();
                if (empty($result)){
                    return true;
                }
                if ($message['return_code'] === 'SUCCESS') {
                    if($message['result_code'] === 'SUCCESS'){
                        $ispay = WechatPay::doPay($this->miniapp_id)->order->queryByOutTradeNumber($message['out_trade_no']);
                        if ($ispay['return_code'] === 'SUCCESS') {
                            if ($ispay['result_code'] === 'SUCCESS') {
                                if ($ispay['trade_state'] === 'SUCCESS') {
                                    //修改订单
                                    $result->paid_at   = 1;
                                    $result->paid_time = strtotime($ispay['time_end']);
                                    $result->paid_no   = $ispay['transaction_id'];
                                    $result->save();
                                    Sale::where(['id' =>$result->sale_id])->update(['is_pay' => 1]);  //修改状态
                                    if(!empty($result->sale)){
                                        $rebate = saleUser::where(['id' =>$result->sale->sales_user_id,'is_rebate' => 0])->find();
                                        if(!empty($rebate)){
                                            //配置
                                            $config = Config::where(['member_miniapp_id' => $this->miniapp_id])->find();
                                            //成本价
                                            $gift = array_column(json_decode($result->sale->gift,true),'cost_price');
                                            $cost_price = array_sum($gift);
                                            //利润
                                            $service_fee = $result->real_amount*$config->profit/100; //服务费
                                            $rebate = $result->real_amount-$service_fee-$cost_price; //成交价-服务费-成本
                                            SaleUser::where(['id' =>$result->sale->sales_user_id])->update(['is_sale' => 0,'is_rebate' => 1,'rebate' => $rebate,'update_time'=> $result->paid_time]);
                                            //日志
                                            BankBill::add($result->member_miniapp_id,$result->sale->user_id,0,'已成交,待结算利润￥'.$rebate,$result->user_id,$result->order_no);
                                        }    
                                    }
                                    //扣款交易服务费
                                    $goodpay_tax = $result->order_amount*0.05;
                                    SystemMemberBankBill::create(['state' => 1,'money' => $goodpay_tax,'member_id' => $this->miniapp->member_id,'message'=> '平台服务费','update_time' => $result->paid_time]);
                                    SystemMemberBank::moneyUpdate($this->miniapp->member_id,-$goodpay_tax);
                                    return true;
                                }
                            }
                        }
                        return $fail('通信失败,请稍后再通知我');
                    }else{
                        return $fail('通信失败,请稍后再通知我');
                    }
                }else{
                    return $fail('通信失败,请稍后再通知我');
                }
            });
            $response->send();
        }catch (Exception $e) {
            $this->error('页面不存在');
        }
    }

    /**
     * 帐号充值
     * @access public
     */
    public function recharge(){
        try {
            $response = WechatPay::doPay($this->miniapp_id)->handlePaidNotify(function($message,$fail){
                $result = BankRecharge::where(['order_no' => $message['out_trade_no'],'state' => 0])->find();
                if (empty($result)){
                    return true;
                }
                if ($message['return_code'] === 'SUCCESS') {
                    if($message['result_code'] === 'SUCCESS'){
                        $ispay = WechatPay::doPay($this->miniapp_id)->order->queryByOutTradeNumber($result->order_no);
                        if ($ispay['return_code'] === 'SUCCESS') {
                            if ($ispay['result_code'] === 'SUCCESS') {
                                if ($ispay['trade_state'] === 'SUCCESS') {
                                    Bank::moneyChange($this->miniapp_id,$result->user_id,$result->money);
                                    BankBill::add($result->member_miniapp_id,$result->user_id,$result->money,'充值￥'.$result->money,0,$result->order_no);
                                    $result->state     = 1;
                                    $result->paid_time = strtotime($ispay['time_end']);
                                    $result->paid_no   = $ispay['transaction_id'];
                                    $result->save();
                                    return true;
                                }
                            }
                        }
                        return $fail('通信失败,请稍后再通知我');
                    }else{
                        return $fail('通信失败,请稍后再通知我');
                    }
                }else{
                    return $fail('通信失败,请稍后再通知我');
                }
            });
            $response->send();
        }catch (Exception $e) {
            $this->error('页面不存在');
        }
    }

   /**
     * 商城购买
     * @access public
     */
    public function shop(){
        try {
            $response = WechatPay::doPay($this->miniapp_id)->handlePaidNotify(function($message,$fail){
                $result = Order::where(['order_no' => $message['out_trade_no'],'state' => 0])->find();
                if (empty($result)){
                    return true;
                }
                if ($message['return_code'] === 'SUCCESS') {
                    if($message['result_code'] === 'SUCCESS'){
                        $ispay = WechatPay::doPay($this->miniapp_id)->order->queryByOutTradeNumber($result->order_no);
                        if ($ispay['return_code'] === 'SUCCESS') {
                            if ($ispay['result_code'] === 'SUCCESS') {
                                if ($ispay['trade_state'] === 'SUCCESS') {
                                    $result->paid_at   = 1;
                                    $result->paid_time = strtotime($ispay['time_end']);
                                    $result->paid_no   = $ispay['transaction_id'];
                                    $result->save();
                                    BankBill::add($result->member_miniapp_id,$result->user_id,$result->money,'下单购买宝贝',0,$result->order_no);
                                    return true;
                                }
                            }
                        }
                        return $fail('通信失败,请稍后再通知我');
                    }else{
                        return $fail('通信失败,请稍后再通知我');
                    }
                }else{
                    return $fail('通信失败,请稍后再通知我');
                }
            });
            $response->send();
        }catch (Exception $e) {
            $this->error('页面不存在');
        }
    } 
}