<?php
namespace app\order\service;
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
     * @param int $payId
     * @param string $payWay
     * @return bool
     */
    public function pay($rechargeNo, $money, $payName, $payNo, $payId = 0, $payWay = 'system') {
        if(!target('order/Order', 'service')->payOrder($rechargeNo, $money, $payName, $payNo, $payId, $payWay)) {
            return $this->error(target('order/Order', 'service')->getError());
        }
        return $this->success();
    }


}
