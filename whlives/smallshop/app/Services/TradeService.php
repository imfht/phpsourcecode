<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/18
 * Time: 下午4:03
 */

namespace App\Services;

use App\Models\Balance;
use App\Models\BalanceDetail;
use App\Models\BalanceRecharge;
use App\Models\Order;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;

class TradeService
{
    /**
     * 生成交易单号
     * @return string
     */
    public static function getTradeNo()
    {
        $order_no = date('YmdHis', time()) . rand(100000, 999999);
        return $order_no;
    }

    /**
     * 生成订单交易单并验证订单信息
     * @param $m_id
     * @param $order_no
     * @return bool|string
     * @throws \App\Exceptions\ApiException
     */
    public static function getOrderInfo($m_id, $order_no)
    {
        if (!$order_no) {
            api_error(__('api.order_error'));
        }
        //开始验证订单
        $order_list = Order::select('status', 'subtotal')->whereIn('order_no', $order_no)->get();
        if ($order_list->isEmpty()) {
            api_error(__('api.order_error'));
        }
        $subtotal = 0;
        foreach ($order_list as $order) {
            //存在已经支付或取消的订单
            if ($order['status'] != Order::STATUS_WAIT_PAY) {
                api_error(__('api.order_pay_status_error'));
            }
            $subtotal += $order['subtotal'];
        }
        //添加交易单
        $trade_no = self::getTradeNo();
        $trade_data = array(
            'm_id' => $m_id,
            'trade_no' => self::getTradeNo(),
            'order_no' => json_encode($order_no),
            'type' => Trade::TYPE_ORDER,
            'subtotal' => $subtotal
        );
        $res = Trade::create($trade_data);
        if ($res) {
            return $trade_data;
        }
        return false;
    }

    /**
     * 生成充值交易单并验证充值信息
     * @param $m_id
     * @param $recharge_no
     * @return bool|string
     * @throws \App\Exceptions\ApiException
     */
    public static function getRechargeInfo($m_id, $recharge_no)
    {
        if (!$recharge_no) {
            api_error(__('api.order_error'));
        }
        //开始验证充值订单
        $recharge = BalanceRecharge::where('recharge_no', $recharge_no)->first();
        if (!$recharge) {
            api_error(__('api.recharge_error'));
        } elseif ($recharge['status'] != BalanceRecharge::STATUS_OFF) {
            api_error(__('api.recharge_status_error'));
        }

        $subtotal = $recharge['amount'];
        //添加交易单
        $trade_no = self::getTradeNo();
        $trade_data = array(
            'm_id' => $m_id,
            'trade_no' => self::getTradeNo(),
            'order_no' => $recharge_no,
            'type' => Trade::TYPE_RECHARGE,
            'subtotal' => $subtotal
        );
        $res = Trade::create($trade_data);
        if ($res) {
            return $trade_data;
        }
        return false;
    }

    /**
     * 支付成功后修改状态
     * @param $member 用户信息
     */
    public static function updatePayStatus($notify_data = array())
    {
        if (!$notify_data) {
            return false;
        }
        //查询交易单
        $trade = Trade::where('trade_no', $notify_data['trade_no'])->first();
        if (!$trade) {
            return false;
        }
        if ($trade['status'] == Trade::STATUS_ON) {
            return true;//已经支付
        }
        //风险标示
        $flag = Trade::FLAG_YES;
        if ($notify_data['pay_total'] >= $trade['subtotal']) {
            $flag = Trade::FLAG_NO;
        }
        //修改交易单状态
        $update_trade = array(
            'status' => Trade::STATUS_ON,
            'payment_id' => $notify_data['payment_id'],
            'payment_no' => $notify_data['payment_no'],
            'pay_total' => $notify_data['pay_total'],
            'flag' => $flag,
            'pay_at' => get_date()
        );
        $res = Trade::where('id', $trade['id'])->update($update_trade);
        if ($res) {
            //支付成功后去修改订单和充值单状态
            $trade_data = [
                'trade_id' => $trade['id'],
                'flag' => $update_trade['flag'],
                'payment_id' => $notify_data['payment_id'],
                'payment_no' => $notify_data['payment_no'],
            ];
            switch ($trade['type']) {
                case Trade::TYPE_ORDER:
                    //订单支付
                    $trade_data['order_no'] = json_decode($trade['order_no'], true);
                    OrderService::updatePayOrder($trade_data);
                    break;
                case Trade::TYPE_RECHARGE:
                    //充值单
                    $trade_data['order_no'] = $trade['order_no'];
                    BalanceRecharge::updatePayStatus($trade_data);
                    break;
            }
            return true;
        } else {
            return false;
        }
        return false;
    }
}
