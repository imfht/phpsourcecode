<?php
namespace app\member\service;
/**
 * 支付订单处理
 */
class PayOrderService extends \app\base\service\BaseService {

    /**
     * 订单支付
     * @param $rechargeNo
     * @param $money
     * @param $payName
     * @param $payNo
     * @return bool
     */
    public function pay($rechargeNo, $money, $payName, $payNo) {
        if(!target('member/Member', 'service')->payRecharge($rechargeNo, $money, $payName, $payNo)) {
            return $this->error(target('member/Member', 'service')->getError());
        }
        return $this->success();
    }
}
