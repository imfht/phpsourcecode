<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\green\controller\api\v1;
use app\green\controller\api\Base;
use app\common\facade\WechatPay;
use app\green\model\GreenOrder;
use app\green\model\GreenUser;
use Exception;

class Notify extends Base{
 
   /**
     * 商城购买
     * @access public
     */
    public function shop(){
        try {
            $response = WechatPay::doPay($this->miniapp_id)->handlePaidNotify(function($message,$fail){
                $result = GreenOrder::where(['order_no' => $message['out_trade_no'],'paid_at' => 0])->find();
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
                                    $info = GreenUser::where(['uid' => $result->user_id])->find();
                                    $info->points      = ['dec', $result->points];
                                    $info->update_time = time();
                                    $info->save();
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