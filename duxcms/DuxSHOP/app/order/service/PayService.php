<?php
namespace app\order\service;
/**
 * 系统支付接口
 */
class PayService {
    /**
     * 获取回调接口
     */
    public function getCallbackPay() {
        return [
            'order' =>  [
                'name' => '订单支付',
                'target' => 'order/PayOrder'
            ]
        ];
    }
}
