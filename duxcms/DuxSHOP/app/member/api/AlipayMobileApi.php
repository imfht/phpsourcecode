<?php

/**
 * 支付宝移动端通知
 */

namespace app\member\api;

class AlipayMobileApi {

    public function index() {
        $config = target('member/AlipayMobile', 'pay')->getConfig();
        try{
            $alipay = \Yansongda\Pay\Pay::alipay($config);
            $data = $alipay->verify();
            if ($data['trade_status'] <> 'TRADE_SUCCESS') {
                dux_log('支付状态失败');
                return false;
            }
            $orderNo = $data['out_trade_no'];
            if (empty($orderNo)) {
                dux_log('支付号错误');
                return false;
            }
            $model = target('member/PayRecharge');

            $app = $data['passback_params'];
            dux_log($app);

            $callbackList = target('member/PayConfig')->callbackList();
            $callbackInfo = $callbackList[$app];

            $model->beginTransaction();
            if(!target($callbackInfo['target'], 'service')->pay($orderNo, $data['total_amount'], '支付宝移动端', $data['trade_no'], 0, 'alipay_mobile')) {
                $model->rollBack();
                dux_log(target($callbackInfo['target'], 'service')->getError());
                return false;
            }
            $model->commit();
            return $alipay->success()->send();
        } catch (\Exception $e) {
            dux_log($e->getMessage());
        }
    }


}