<?php

/**
 * 支付宝支付
 */

namespace app\member\mobile;

class AlipayMobile extends \app\base\mobile\SiteMobile {


    public function index() {
        $token = request('get', 'token');
        $data = request('get');
        $data['title'] = urldecode($data['title']);
        $data['body'] = urldecode($data['body']);
        unset($data['token']);
        if(!data_sign_has($data, $token)) {
            $this->error('token验证失败!');
        }
        $data['return_url'] = urldecode($data['return_url']);

        if($data['tmp'] + 600 < time()) {
            $this->error('支付已过期，请重新支付！');
        }

        //扫码支付
        $notifyUrl = url('api/member/AlipayMobile/index', [], true);

        $config = target('member/AlipayMobile', 'pay')->getConfig($notifyUrl);

        if(empty($config)) {
            $this->error('请先配置支付信息');
        }

        $payData = [
            'out_trade_no' => $data['order_no'],
            'total_amount' => $data['money'] ? price_format($data['money']) : 0,
            'subject' => str_len($data['title'] ? $data['title']: $data['body'], 125),
            'passback_params' => $data['app'],
        ];
        if (empty($payData['out_trade_no'])) {
            $this->error('订单号不能为空!');
        }
        if ($payData['total_amount'] <= 0) {
            $this->error('支付金额不正确!');
        }
        if (empty($payData['subject'])) {
            $this->error('支付信息描述不正确!');
        }
        if (empty($payData['passback_params'])) {
            $this->error('订单应用名不正确!');
        }
        try {
            \Yansongda\Pay\Pay::alipay($config)->wap($payData)->send();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

}