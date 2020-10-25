<?php
namespace app\member\pay;
/**
 * 支付宝WEB端服务
 */
class AlipayWebPay extends \app\base\service\BaseService {


    public function getConfig($notifyUrl = '') {
        $config = target('member/PayConfig')->getConfig('alipay_web');
        if (empty($config['partner']) || empty($config['key'])) {
            return $this->error('请先配置支付接口信息!');
        }
        $config = [
            'app_id' => $config['appid'],
            'ali_public_key' => $config['public_key'],
            'private_key' => $config['private_key'],
            'notify_url' => $notifyUrl,
        ];
        return $config;
    }

    public function getData($data, $returnUrl) {
        if (empty($data)) {
            return $this->error('订单数据未提交!');
        }
        unset($data['user_id']);
        $data['return_url'] = urlencode(DOMAIN . $returnUrl);
        $data['tmp'] = time();
        $data['token'] = data_sign($data);
        $url = url('controller/member/Alipay/index') . '?' . http_build_query($data);
        return $this->success([
            'url' => $url
        ]);
    }

    public function addLog($payData) {
        $data = [];
        $data['user_id'] = $payData['user_id'];
        $data['pay_no'] = $payData['pay_no'];
        $data['pay_name'] = $payData['pay_name'];
        $data['type'] = 0;
        $data['deduct'] = 0;
        $data['title'] = $payData['title'];
        $data['remark'] = $payData['remark'];
        $data['money'] = $payData['money'];
        $payId = target('member/Finance', 'service')->account($data);
        if (!$payId) {
            return $this->error(target('member/Finance', 'service')->getError());
        }
        return $payId;
    }

    public function getLog($id) {
        return target('member/PayLog')->getInfo($id);
    }

    public function refund($data) {
        $payInfo = target('member/PayLog')->getInfo($data['id']);
        if(empty($payInfo)) {
            return $this->error('未发现支付记录!');
        }
        $payData = [
            'trade_no' => $payInfo['pay_no'],
            'refund_reason' => $data['remark'],
            'refund_amount' => $data['money'] ? $data['money'] : $payInfo['money'],
            'out_trade_no' => $payInfo['log_no'],
        ];

        if (bccomp(0, $payData['refund_amount'], 2) !== -1) {
            return $this->error('退款金额不正确!');
        }
        if (empty($payData['trade_no']) && empty($payData['out_trade_no'])) {
            return $this->error('退款单号不正确!');
        }
        $config = $this->getConfig();
        try {
            $return = \Yansongda\Pay\Pay::alipay($config)->refund($payData);
            $finaceData = [];
            $finaceData['user_id'] = $data['user_id'];
            $finaceData['pay_no'] = $return['trade_no'];
            $finaceData['pay_name'] = '支付宝网页版';
            $finaceData['type'] = 1;
            $finaceData['deduct'] = 0;
            $finaceData['title'] = $data['title'];
            $finaceData['remark'] = $data['remark'];
            $finaceData['money'] = $payData['refund_fee'];
            $payId = target('member/Finance', 'service')->account($finaceData);
            if (!$payId) {
                return $this->error(target('member/Finance', 'service')->getError());
            }
            return $this->success($return);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


}