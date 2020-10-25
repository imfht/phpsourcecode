<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/25
 * Time: 上午10:28
 */

namespace App\Libs\Payment;

use App\Models\Payment;
use App\Services\TokenService;
use App\Services\TradeService;
use EasyWeChat\Factory;

/**
 * 微信支付
 * Class Wechat
 * @package App\Libs\Payment
 */
class Wechat
{
    private $wxpay;

    public function __construct()
    {
        $this->platform = get_platform();
        if (in_array($this->platform, array('mp', 'web', 'h5'))) {
            $config_data = config('weixin.mp');
        } elseif (in_array($this->platform, array('wechat'))) {
            $config_data = config('weixin.wechat');
        } else {
            $config_data = config('weixin.app');
        }
        $config = [
            //必要配置
            'app_id' => $config_data['appid'],
            'mch_id' => $config_data['mch_id'],
            'key' => $config_data['api_key'],// API 密钥
            //如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path' => $config_data['sslcert_path'], // XXX: 绝对路径！！！！
            'key_path' => $config_data['sslkey_path'],  // XXX: 绝对路径！！！！
            'notify_url' => url('/v1/pay/notify/' . Payment::PAYMENT_WECHAT) . '/' . $this->platform,// 你也可以在下单时单独设置来想覆盖它
        ];
        $this->wxpay = Factory::payment($config);
    }

    /**
     * @param $trade_data
     * @return array
     * @throws WxPayException
     */
    public function getPayData($trade_data)
    {
        if (!$trade_data['title'] || !$trade_data['trade_no'] || !$trade_data['subtotal']) {
            api_error(__('api.missing_params'));
        }
        $trade_type = 'APP';//默认app支付
        $openid = '';
        if (in_array($this->platform, array('mp', 'wechat'))) {
            //获取openid，在登陆的时候已经存到token
            $token_service = new TokenService('api');
            $token_data = $token_service->getToken();
            if (!$token_data || !$token_data['openid']) {
                api_error(__('api.pay_openid_error'));
            }
            $openid = $token_data['openid'];
            $trade_type = 'JSAPI';
        } elseif (in_array($this->platform, array('web'))) {
            $trade_type = 'NATIVE';
        } elseif (in_array($this->platform, array('h5'))) {
            $trade_type = 'MWEB';
        }

        $prepay_data = $this->getPrepayData($trade_data, $trade_type, $openid);
        if (is_array($prepay_data)) {
            $prepay_id = $prepay_data['prepay_id'];
            $jssdk = $this->wxpay->jssdk;
            $pay_data = array();
            switch ($trade_type) {
                case 'JSAPI':
                    $pay_data = $jssdk->bridgeConfig($prepay_id, false);
                    break;
                case 'NATIVE':
                    $pay_data['code_url'] = $prepay_data['code_url'];
                    break;
                case 'MWEB':
                    $pay_data['mweb_url'] = $prepay_data['mweb_url'];
                    break;
                default:
                    $pay_data = $jssdk->appConfig($prepay_id);
                    break;
            }
            return $pay_data;
        } else {
            return $prepay_data;
        }
    }

    /**
     * 统一下单
     * @param $trade_data 交易单信息
     * @param $trade_type 交易类型
     * @param string $openid 用户openid
     * @return array|\EasyWeChat\Kernel\Support\Collection|mixed|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getPrepayData($trade_data, $trade_type, $openid = '')
    {
        $result = $this->wxpay->order->unify([
            'body' => $trade_data['title'],
            'out_trade_no' => $trade_data['trade_no'],
            'total_fee' => $trade_data['subtotal'] * 100,
            'trade_type' => $trade_type,
            'openid' => $openid,
        ]);
        if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            return $result;
        } else {
            if ($result['return_code'] !== 'SUCCESS') {
                return $result['return_msg'];
            } elseif ($result['result_code'] !== 'SUCCESS') {
                return $result['err_code_des'];
            }
        }
    }

    /**
     * 服务端回调验证
     * @return string
     */
    public function notify()
    {
        $response = $this->wxpay->handlePaidNotify(function($message, $fail){
            //查询微信订单是否支付
            $pay_data = $this->wxpay->order->queryByOutTradeNumber($message['out_trade_no']);
            if ($pay_data['return_code'] === 'SUCCESS' && $pay_data['result_code'] === 'SUCCESS' && isset($pay_data['trade_state'])) {
                $return = array(
                    'trade_no' => $message['out_trade_no'],
                    'pay_total' => format_price(round($message['total_fee'] / 100, 2)),
                    'payment_no' => $message['transaction_id'],
                    'payment_id' => Payment::PAYMENT_WECHAT
                );
                $res = TradeService::updatePayStatus($return);
                if ($res) {
                    return true;
                } else {
                    $fail('fail');
                }
            } else {
                if ($pay_data['return_code'] !== 'SUCCESS') {
                    $fail($pay_data['return_msg']);
                } elseif ($pay_data['result_code'] !== 'SUCCESS') {
                    $fail($pay_data['err_code_des']);
                }
            }
        });
        return $response;
    }

    /**
     * 退款提交
     * @param $refund_info 退款信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|mixed|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function refund($refund_info)
    {
        if (!$refund_info['payment_no'] || !$refund_info['refund_no'] || !$refund_info['pay_total'] || !$refund_info['refund_amount']) {
            api_error(__('api.missing_params'));
        }
        //参数分别为：微信订单号、商户退款单号、订单金额、退款金额
        $result = $this->wxpay->refund->byTransactionId( $refund_info['payment_no'], $refund_info['refund_no'], $refund_info['pay_total'], $refund_info['refund_amount']);
        if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            return $result;
        } else {
            if ($result['return_code'] !== 'SUCCESS') {
                return $result['return_msg'];
            } elseif ($result['result_code'] !== 'SUCCESS') {
                return $result['err_code_des'];
            }
        }
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
