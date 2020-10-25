<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2019/02/26
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\V1;

use App\Models\Payment;
use App\Models\Trade;
use App\Services\TradeService;
use Illuminate\Http\Request;

class PayController extends BaseController
{

    /**
     * 获取支付方式
     * @param Request $request
     * @return array
     */
    public function payment(Request $request)
    {
        $type = (int)$request->post('type', Trade::TYPE_ORDER);
        $payment = Payment::getPayment($type);
        return $this->success($payment);
    }

    /**
     * 订单支付，支付多订单批量支付
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function payData(Request $request)
    {
        $m_id = $this->getUserId();
        $type = (int)$request->post('type', Trade::TYPE_ORDER);
        $order_no = $request->post('order_no');
        $payment_id = (int)$request->post('payment_id');
        $return_url = $request->post('return_url');//支付宝网页支付的时候需要

        if (!$type || !$order_no || !$payment_id) {
            api_error(__('api.missing_params'));
        }

        //余额充值不支持余额支付
        if ($type == Trade::TYPE_RECHARGE && $payment_id == Payment::PAYMENT_BALANCE) {
            api_error(__('api.recharge_not_balance_pay'));
        }

        //验证支付方式是否存在
        $payment = Payment::find($payment_id);
        $platform = get_platform();
        if (!$payment || $payment['status'] != Payment::STATUS_ON || !in_array($platform, explode(',', $payment['client_type']))) {
            api_error(__('api.payment_error'));
        }
        $trade = '';
        $title = '';
        switch ($type) {
            case Trade::TYPE_ORDER:
                $title = '订单支付';
                $trade = TradeService::getOrderInfo($m_id, explode(',', $order_no));
                break;
            case  Trade::TYPE_RECHARGE:
                $title = '充值订单';
                $trade = TradeService::getRechargeInfo($m_id, $order_no);
                break;
        }
        if ($trade) {
            $pay_info = array(
                'title' => $title,
                'm_id' => $trade['m_id'],
                'type' => $trade['type'],
                'subtotal' => $trade['subtotal'],
                'trade_no' => $trade['trade_no'],
                'return_url' => $return_url
            );
            $pay_data = array();
            if ($trade['subtotal'] > 0) {
                //请求支付信息
                $class_name = '\App\Libs\Payment\\' . $payment['class_name'];
                $pay = new $class_name();
                $pay_data = $pay->getPayData($pay_info);
                if ($pay_data && is_array($pay_data)) {
                    $pay_data['trade_no'] = $trade['trade_no'];
                    if (!isset($pay_data['is_pay'])) {
                        $pay_data['is_pay'] = 1;
                    }
                } else {
                    api_error(__($pay_data));
                }
            } else {
                $pay_data['is_pay'] = 0;
                $pay_data['trade_no'] = $trade['trade_no'];
            }
            //不需要支付的修改支付状态
            if ($pay_data['is_pay'] == 0) {
                $pay_status_res = TradeService::updatePayStatus($pay_data);
                if (!$pay_status_res) {
                    api_error(__('api.fail'));
                }
            }
            return $this->success($pay_data);
        } else {
            api_error(__('api.trade_create_fail'));
        }
    }

    /**
     * 服务端支付回调
     * @param Request $request
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public function notify(Request $request)
    {
        $payment_id = (int)$request->payment_id;
        //验证支付方式
        $payment = Payment::where(['id' => $payment_id, 'status' => Payment::STATUS_ON])->first();
        if (!$payment) {
            api_error(__('api.payment_error'));
        }

        $class_name = '\App\Libs\Payment\\' . $payment['class_name'];
        $pay = new $class_name();
        $res_data = $pay->notify();
        if (is_array($res_data)) {
            //修改交易单和订单状态
            $res = TradeService::updatePayStatus($res_data);
            if ($res) {
                return $pay->success();
            } else {
                return $pay->fail();
            }
        } else {
            return $pay->fail();
        }
    }

    /**
     * 查询交易单是否支付成功
     * @param Request $request
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public function tradeStatus(Request $request)
    {
        $trade_no = $request->post('trade_no');
        if (!$trade_no) {
            api_error(__('api.missing_params'));
        }
        $trade = Trade::where('trade_no', $trade_no)->first();
        if ($trade && $trade['status'] == Trade::STATUS_ON) {
            return $this->success(true);
        } else {
            return $this->success(false);
        }
    }

}
