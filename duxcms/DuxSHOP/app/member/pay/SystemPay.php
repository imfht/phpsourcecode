<?php

namespace app\member\pay;
/**
 * 余额支付
 */
class SystemPay extends \app\base\service\BaseService {


    public function getData($payInfo, $returnUrl) {
        $orderPayNo = $payInfo['order_no'];
        $data = [];
        $data['user_id'] = $payInfo['user_id'];
        $data['pay_no'] = $orderPayNo;
        $data['pay_name'] = '账号支付';
        $data['type'] = 0;
        $data['deduct'] = 1;
        $data['title'] = $payInfo['title'];
        $data['remark'] = $payInfo['body'];
        $data['money'] = $payInfo['money'];
        $data['order_no'] = $payInfo['money'];

        $payId = target('member/Finance', 'service')->account($data);
        if (!$payId) {
            return $this->error(target('member/Finance', 'service')->getError());
        }
        $app = $payInfo['app'];
        $callbackList = target('member/PayConfig')->callbackList();
        $callbackInfo = $callbackList[$app];
        if(!target($callbackInfo['target'], 'service')->pay($orderPayNo, $payInfo['money'], '账号支付', $orderPayNo, $payId, 'system')) {
            dux_log(target($callbackInfo['target'], 'service')->getError());
            return $this->error(target($callbackInfo['target'], 'service')->getError());
        }
        return $this->success([
            'url' => $returnUrl,
            'complete' => true
        ]);
    }

    public function getLog($id) {
        return target('member/PayLog')->getInfo($id);
    }

    public function refund($payData) {
        $info = target('member/PayLog')->getWhereInfo([
            'A.log_id' => $payData['id']
        ]);
        if(empty($info)) {
            return $this->error('支付单不存在，无法退款!');
        }
        $data = [];
        $data['user_id'] = $payData['user_id'];
        $data['pay_name'] = '余额支付';
        $data['type'] = 1;
        $data['deduct'] = 1;
        $data['title'] = $payData['title'];
        $data['remark'] = $payData['remark'];
        $data['money'] = $payData['money'] ? $payData['money'] : $info['money'];
        if (!target('member/Finance', 'service')->account($data)) {
            return $this->error(target('member/Finance', 'service')->getError());
        }
        return $this->success();
    }


}