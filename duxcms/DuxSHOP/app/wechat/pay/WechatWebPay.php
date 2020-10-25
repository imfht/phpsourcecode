<?php
namespace app\wechat\pay;
/**
 * 微信移动端服务
 */
class WechatWebPay extends \app\base\service\BaseService {

    private $rsa = '';

    public function getConfig($notifyUrl = '') {
        $config = target('member/PayConfig')->getConfig('wechat_web');
        if (empty($config['mch_id']) || empty($config['md5_key'])) {
            return $this->error('请先配置支付接口信息!');
        }
        $notifyUrl = DOMAIN . $notifyUrl;
        return [
            'app_id' => $config['app_id'],
            'mch_id' => $config['mch_id'],
            'key' => $config['md5_key'],
            'cert_client' => ROOT_PATH . $config['app_cert_pem_file'],
            'cert_key' => ROOT_PATH . $config['app_key_pem_file'],
            'notify_url' => $notifyUrl,
        ];
    }

    public function getData($data, $returnUrl) {
        if (empty($data)) {
            return $this->error('订单数据未提交!');
        }
        unset($data['user_id']);
        $data['return_url'] = urlencode(DOMAIN . $returnUrl);
        $data['tmp'] = time();
        $data['token'] = data_sign($data);
        $url = url('controller/wechat/WebPay/index') . '?' . http_build_query($data);
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
        if (empty($payInfo)) {
            return $this->error('未发现支付记录!');
        }

        $money = $data['money'] ? $data['money'] : $payInfo['money'];
        $money = price_calculate($money, '*', 100, 0);
        $payData = [
            'out_trade_no' => $payInfo['pay_no'],
            'total_fee' => price_calculate($payInfo['money'], '*', 100, 0),
            'refund_fee' => $money,
            'out_refund_no' => $payInfo['log_no'],
        ];
        if ($payData['refund_fee'] <= 0) {
            return $this->error('退款金额不正确!');
        }
        if (empty($payData['out_trade_no']) && empty($payData['out_refund_no'])) {
            return $this->error('退款单号不正确!');
        }
        $config = $this->getConfig();
        try {
            $return = \Yansongda\Pay\Pay::wechat($config)->refund($payData);
            $finaceData = [];
            $finaceData['user_id'] = $data['user_id'];
            $finaceData['pay_no'] = $return['refund_id'];
            $finaceData['pay_name'] = '微信扫码支付';
            $finaceData['type'] = 1;
            $finaceData['deduct'] = 0;
            $finaceData['title'] = $data['title'];
            $finaceData['remark'] = $data['remark'];
            $finaceData['money'] = $money;
            $payId = target('member/Finance', 'service')->account($finaceData);
            if (!$payId) {
                return $this->error(target('member/Finance', 'service')->getError());
            }
            return $this->success($payId);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}