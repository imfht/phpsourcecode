<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 系统支付回调
 */
namespace app\system\controller\api\v1;
use app\common\facade\WechatPay;
use app\common\model\SystemMemberBank;
use app\common\model\SystemMemberBankBill;
use app\common\model\SystemMemberBankRecharge;
use think\Controller;
use Exception;

class Notify extends Controller{

    /**
     * 服务商统一回调通知
     * @return void
     */
    public function index(){
        try {
             $response = WechatPay::doPay()->handlePaidNotify(function($message,$fail){
                $rel = SystemMemberBankRecharge::where(['order_sn' =>$message['out_trade_no'],'state' => 0])->find();
                if (empty($rel)) {
                    return true;
                }
                if ($message['result_code'] === 'SUCCESS') {
                    if($message['result_code'] === 'SUCCESS'){
                        $ispay = WechatPay::doPay()->order->queryByOutTradeNumber($rel->order_sn);
                        if ($ispay['return_code'] === 'SUCCESS') {
                            if ($ispay['result_code'] === 'SUCCESS') {
                                if ($ispay['trade_state'] === 'SUCCESS'){
                                    if($ispay['total_fee'] == $rel->money * 100){
                                        $rel->state =1;
                                        $rel->update_time = time();
                                        $rel->transaction_id = $ispay['transaction_id'];;
                                        $rel->save();
                                    }
                                    $memberBankBill = new SystemMemberBankBill();
                                    $memberBankBill->money       = $ispay['total_fee']/100;
                                    $memberBankBill->member_id   = $rel->member_id;
                                    $memberBankBill->update_time = time();
                                    $memberBankBill->state       = 0;
                                    $memberBankBill->save();
                                    SystemMemberBank::moneyUpdate($rel->member_id,$ispay['total_fee']/100);
                                }
                            }
                        }
                    }
                }
                return $fail('通信失败,请稍后再通知我');
            });
            $response->send();
        }catch (Exception $e) {
            $this->error('页面不存在');
        }
    }
}