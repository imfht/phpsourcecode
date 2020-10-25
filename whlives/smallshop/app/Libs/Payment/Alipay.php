<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/10
 * Time: 下午1:42
 */

namespace App\Libs\Payment;

use App\Models\Payment;

require_once(dirname(__FILE__) . '/alipay_php_sdk/AopSdk.php');

/**
 * 支付宝支付
 * Class Alipay
 * @package App\Libs\Payment
 */
class Alipay
{
    protected $aop;

    public function __construct()
    {
        $this->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $this->appId = config('payment.alipay.appid');
        $this->rsaPrivateKey = config('payment.alipay.private_key');
        $this->alipayrsaPublicKey = config('payment.alipay.public_key');
        $this->notify_url = url('/v1/pay/notify/' . Payment::PAYMENT_ALIPAY);
        $this->return_url = '';
        $this->init();
    }

    /**
     * 初始化参数
     */
    public function init()
    {
        $aop = new \AopClient();
        $aop->gatewayUrl = $this->gatewayUrl;
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $this->aop = $aop;
    }

    /**
     * 请求支付参数
     * @param $trade_data 订单信息
     * @return string|\提交表单HTML文本
     * @throws \Exception
     */
    public function getPayData($trade_data)
    {
        if (!$trade_data['title'] || !$trade_data['trade_no'] || !$trade_data['subtotal']) {
            api_error(__('api.missing_params'));
        }
        $bizcontent = array(
            'body' => $trade_data['title'],
            'subject' => $trade_data['title'],
            'out_trade_no' => $trade_data['trade_no'],
            'timeout_express' => '30m',
            'total_amount' => $trade_data['subtotal']
        );
        $bizcontent = json_encode($bizcontent);
        $platform = get_platform();

        if (in_array($platform, array('web', 'h5'))) {
            if ($trade_data['return_url']) {
                $this->return_url = $trade_data['return_url'];
            }
            //web支付
            $request = new \AlipayTradeWapPayRequest();
            $request->setBizContent($bizcontent);
            $request->setNotifyUrl($this->notify_url);
            $request->setReturnUrl($this->return_url);
            $response = $this->aop->pageExecute($request, 'GET');
        } else {
            //app支付
            $request = new \AlipayTradeAppPayRequest();
            $request->setBizContent($bizcontent);
            $request->setNotifyUrl($this->notify_url);
            $response = $this->aop->sdkExecute($request);
        }
        $pay_data['alipay'] = $response;
        return $pay_data;
    }

    /**
     * 退款提交
     * @param $refund_info 退款信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|mixed|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function refund($refund_info)
    {
        if (!$refund_info['payment_no'] || !$refund_info['refund_no'] || !$refund_info['refund_amount']) {
            api_error(__('api.missing_params'));
        }
        $bizcontent = array(
            'trade_no' => $refund_info['payment_no'],
            'refund_amount' => $refund_info['refund_amount'],
            'out_request_no' => $refund_info['refund_no']
        );
        $bizcontent = json_encode($bizcontent);
        $request = new \AlipayTradeRefundRequest ();
        $request->setBizContent($bizcontent);
        $result = $this->aop->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            $pay_data['status'] = true;
            return $pay_data;
        } else {
            return __('api.fail');
        }
    }

    /**
     * 服务端回调验证
     * @return string
     */
    public function notify()
    {
        $post_data = request()->post();
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $result = $aop->rsaCheckV1($post_data, NULL, "RSA2");
        if ($result) {
            if ($post_data['trade_status'] != 'TRADE_SUCCESS') {
                return false;
            }
        }
        $return = array(
            'trade_no' => $post_data['out_trade_no'],
            'pay_total' => format_price($post_data['total_amount']),
            'payment_no' => $post_data['trade_no'],
            'payment_id' => Payment::PAYMENT_ALIPAY
        );
        return $return;
    }

    /**
     * 支付成功
     * @return string
     */
    public function success()
    {
        return 'success';
    }

    /**
     * 支付失败
     * @return string
     */
    public function fail()
    {
        return 'fail';
    }
}