<?php
namespace app\wechat\pay;
/**
 * 微信移动端服务
 */
class WechatMobilePay extends \app\base\service\BaseService {


    public function getConfig($notifyUrl = '') {
        $config = target('member/PayConfig')->getConfig('wechat_mobile');
        if (empty($config['mch_id']) || empty($config['md5_key'])) {
            return $this->error('请先配置支付接口信息!');
        }
        $notifyUrl = DOMAIN . $notifyUrl;
        return [
            'app_id' => $config['app_id'],
            'mch_id' => $config['mch_id'],
            'key' => $config['md5_key'],
            'secret' => $config['secret'],
            'cert_client' => ROOT_PATH . $config['app_cert_pem_file'],
            'cert_key' => ROOT_PATH . $config['app_key_pem_file'],
            'notify_url' => $notifyUrl,
        ];
    }

    public function getData($data, $returnUrl) {
        $payData = [
            'body' => $data['title'] ? $data['title']: $data['body'],
            'order_no' => $data['order_no'],
            'money' => $data['money'],
            'app' => $data['app'],
            'ip' => \dux\lib\Client::getUserIp(),
            'url' => urlencode(DOMAIN . $returnUrl)
        ];
        if (empty($payData['order_no'])) {
            return $this->error('订单号不能为空!');
        }
        if ($payData['amount'] <= 0) {
            return $this->error('支付金额不正确!');
        }
        if (empty($payData['subject']) || empty($payData['body'])) {
            return $this->error('支付信息描述不正确!');
        }
        if (empty($payData['return_param'])) {
            return $this->error('订单应用名不正确!');
        }

        $payData['token'] = data_sign($payData);
        if(isWechat()) {
            $url = url('mobile/wechat/Pay/auth') . '?' . http_build_query($payData);
        }else {
            $url = url('mobile/wechat/Pay/browser') . '?' . http_build_query($payData);
        }
        return $this->success([
            'url' => $url
        ]);
    }

    public function getParams($data) {
        if (empty($data)) {
            return $this->error('订单数据未提交!');
        }

        $notifyUrl = url('api/wechat/WechatMobile/index');
        $config = $this->getConfig($notifyUrl);

        $money = $data['money'] ? $data['money'] : 0;
        $money = price_calculate($money, '*', 100, 0);
        $payData = [
            'body' => $data['title'] ? $data['title']: $data['body'],
            'out_trade_no' => $data['order_no'],
            'total_fee' => $money,
            'attach' => $data['app'],
            'spbill_create_ip' => \dux\lib\Client::getUserIp(),
            'openid' => $data['openid'],
        ];
        try {
            $pay = \Yansongda\Pay\Pay::wechat($config)->mp($payData);
            return $this->success([
                'appId' => $pay->appId,
                'timeStamp' => $pay->timeStamp,
                'nonceStr' => $pay->nonceStr,
                'package' => $pay->package,
                'signType' => $pay->signType,
                'paySign' => $pay->paySign,
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

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
        if (empty($payData['transaction_id'])) {
            return $this->error('退款单号不正确!');
        }
        $config = $this->getConfig();
        try {
            $return = \Yansongda\Pay\Pay::wechat($config)->refund($payData);
            $finaceData = [];
            $finaceData['user_id'] = $data['user_id'];
            $finaceData['pay_no'] = $return['refund_id'];
            $finaceData['pay_name'] = '微信公众号';
            $finaceData['type'] = 1;
            $finaceData['deduct'] = 0;
            $finaceData['title'] = $data['title'];
            $finaceData['remark'] = $data['remark'];
            $finaceData['money'] = $money;
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
