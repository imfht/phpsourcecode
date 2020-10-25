<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/25
 * Time: 上午10:28
 */

namespace App\Libs\Payment;

use App\Models\BalanceDetail;
use App\Models\Member;
use App\Models\Payment;
use App\Services\TradeService;
use Illuminate\Support\Facades\Hash;

/**
 * 余额支付
 * Class Balance
 * @package App\Libs\Payment
 */
class Balance
{

    /**
     * 开始支付
     * @param $trade_data
     * @return array
     * @throws WxPayException
     */
    public function getPayData($trade_data)
    {
        if (!$trade_data['m_id'] || !$trade_data['trade_no'] || !$trade_data['subtotal'] || !$trade_data['type']) {
            api_error(__('api.missing_params'));
        }

        //判断支付密码
        $pay_password = request()->post('pay_password');
        if (!$pay_password) {
            api_error(__('api.pay_password_error'));
        }
        $member_data = Member::find($trade_data['m_id']);
        if (empty($member_data['pay_password'])) {
            api_error(__('api.pay_password_notset'));
        }
        if (!Hash::check($pay_password, $member_data['pay_password'])) {
            api_error(__('api.pay_password_error'));
        }
        //开始扣除余额
        $note = '支付交易单' . $trade_data['trade_no'];
        $res_balance = \App\Models\Balance::updateAmount($trade_data['m_id'], -$trade_data['subtotal'], BalanceDetail::EVENT_ORDER_PAY, $trade_data['trade_no'], $note);
        if ($res_balance['status']) {
            //支付成功修改交易单状态
            $return = array(
                'trade_no' => $trade_data['trade_no'],
                'pay_total' => $trade_data['subtotal'],
                'payment_no' => $trade_data['trade_no'],
                'payment_id' => Payment::PAYMENT_BALANCE,
                'is_pay' => 0
            );
            return $return;
        } else {
            return $res_balance['message'];
        }
    }

    /**
     * 退款提交
     * @param $refund_info 退款信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|mixed|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function refund($refund_info)
    {
        if (!$refund_info['m_id'] || !$refund_info['refund_amount'] || !$refund_info['refund_no']) {
            api_error(__('api.missing_params'));
        }
        //退款单号、退款金额
        $res_balance = \App\Models\Balance::updateAmount($refund_info['m_id'], $refund_info['refund_amount'], BalanceDetail::EVENT_ORDER_REFUND, $refund_info['refund_no'], $refund_info['note']);
        if ($res_balance['status']) {
            return $res_balance;
        } else {
            return $res_balance['message'];
        }
    }

    /**
     * 支付成功
     * @return string
     */
    public function success()
    {
        return 'success';
    }

    /**
     * 支付失败
     * @return string
     */
    public function fail()
    {
        return 'fail';
    }
}