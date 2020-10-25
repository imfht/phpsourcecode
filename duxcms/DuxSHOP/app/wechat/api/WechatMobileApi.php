<?php

/**
 * 支付宝公众号支付
 */

namespace app\wechat\api;

class WechatMobileApi {

    /**
     * 异步回调
     */
    public function index() {
        $config = target('wechat/WechatMobile', 'pay')->getConfig();
        try{
            $wechat = \Yansongda\Pay\Pay::wechat($config);
            $data = $wechat->verify();
            if ($data['return_code'] <> 'SUCCESS') {
                dux_log('支付状态失败');
                return false;
            }
            $orderNo = $data['out_trade_no'];
            if (empty($orderNo)) {
                dux_log('支付号错误');
                return false;
            }
            $model = target('member/PayRecharge');

            $app = $data['attach'];
            dux_log($app);

            $callbackList = target('member/PayConfig')->callbackList();
            $callbackInfo = $callbackList[$app];

            $model->beginTransaction();
            if(!target($callbackInfo['target'], 'service')->pay($orderNo, price_calculate($data['total_fee'], '/', 100), '微信公众号', $data['transaction_id'], 0, 'wechat_mobile')) {
                $model->rollBack();
                dux_log(target($callbackInfo['target'], 'service')->getError());
                return false;
            }
            $model->commit();
            return $wechat->success()->send();
        } catch (\Exception $e) {
            dux_log($e->getMessage());
        }
    }

}