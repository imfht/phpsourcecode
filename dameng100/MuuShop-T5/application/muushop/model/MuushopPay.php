<?php
namespace app\muushop\model;

use think\Model;

/**
 * 支付
 */
class MuushopPay extends Model{

    //支付方式类型
    protected $payment;

    function __construct(){

        $this->payment = [
            'onlinepay'     => '在线支付',
            'delivery'  => '货到付款'
        ];
    }
    /**
     * 根据配置数据获取支付方式数组
     * @return [type] [description]
     */
    public function getPaytype($payment_config=''){
        //支付方式类型
        $payment = $this->payment;
        //如果$payment_config留空就返回所有
        if(empty($payment_config)){
            return $payment;
        }
        //获取传入配置数据的支付方式
        $payment_array = explode(',',$payment_config);

        $result = [];
        foreach ($payment_array as $k => $v) {
            
            if(array_key_exists($v,$payment)){
                $result[] = ['title' => $this->payTypeStr($v),'value'=>$v];
            }
        }
        
        return $result;
    }
    /**
     * 根据配置数据获取支付方式数组
     * @return [type] [description]
     */
    public function getChannel($channel=''){
        
        $result = $this->channel_info($channel);

        return $result;
    }

    /**
     * 支付方式转文字
     * @param  integer $status [description]
     * @param  string 售后类型 exchange 换货 return 退货
     * @return [type]          [description]
     */
    public function payTypeStr($pay_type = 0)
    {
        switch($pay_type){
           
            case 'onlinepay':
                $pay_type_str = '在线支付';
            break;

            case 'delivery':
                $pay_type_str = '货到付款';
            break;
            
            default:
                $pay_type_str = '状态码'.$pay_type;
        }
        return $pay_type_str;
    }
    /**
     * 获取支付渠道
     * @return [type] [description]
     */
    public function channel()
    {
        $channel_arr = ['alipay','wxpay'];
        $result = [];
        foreach ($channel_arr as $v) { 
            $result[] = $this->channel_info($v);
        }

        return $result;

    }
    /**
     * 获取支付方式详细
     * @param  string $pay_type [description]
     * @return [type]           [description]
     */
    public function channel_info($pay_type = 'alipay')
    {
        $channel = $this->_channel();

        $payChannel = '';

        switch($pay_type){

            case 'alipay':

                if(request()->isMobile()){   
                    $payChannel = $channel['alipay_wap'];
                }else{
                    $payChannel = $channel['alipay_pc_direct'];
                }

            break;

            case 'wxpay':

                if(isWeixinBrowser()){   
                    $payChannel = $channel['wx_pub'];
                }else{
                    $payChannel = $channel['wx_pub_qr'];
                }

            break;
        }

        return $payChannel;
    }
    
    public function _channel($key = 'all')
    {
        $pay_channel = [

            'alipay_app' => [
                'type'   => 'onlinepay',
                'name'   => 'alipay',
                'title'  => '支付宝app支付',
                'stitle' => '支付宝',
                'icon'   => 'alipay.png',
            ],
            'alipay_wap' => [
                'type'   => 'onlinepay',
                'name'   =>'alipay_wap',
                'title'  =>'支付宝手机网页支付',
                'stitle' =>'支付宝',
                'icon'   =>'alipay.png',
            ],
            'alipay_pc_direct' => [
                'type'   => 'onlinepay',
                'name'   =>'alipay_pc_direct',
                'title'  =>'支付宝 PC 网页支付',
                'stitle' =>'支付宝',
                'icon'   =>'alipay.png',
            ],
            'alipay_qr' => [
                'type'   => 'onlinepay',
                'name'   =>'alipay_qr',
                'title'  =>'支付宝当面付',
                'stitle' =>'支付宝扫码',
                'icon'   =>'alipay.png',
            ],
            'wx_app' => [
                'type'   => 'onlinepay',
                'name'   =>'wx',
                'title'  =>'微信 APP 支付',
                'stitle' =>'微信支付',
                'icon'   =>'wxpay.png',
            ],
            'wx_pub' => [
                'type'   => 'onlinepay',
                'name'   =>'wx_pub',
                'title'  =>'微信公众号支付',
                'stitle' =>'微信支付',
                'icon'   =>'wxpay.png',
            ],
            'wx_pub_qr' => [
                'type'   => 'onlinepay',
                'name'   =>'wx_pub_qr',
                'title'  =>'微信公众号扫码支付',
                'stitle' =>'微信扫码',
                'icon'   =>'wxpay.png',
            ],
            'wx_wap' => [
                'type'   => 'onlinepay',
                'name'   =>'wx_wap',
                'title'  =>'微信 WAP 支付',
                'stitle' =>'微信支付',
                'icon'   =>'wxpay.png',
            ],
            'delivery' => [
                'type'   => 'delivery',
                'name'   =>'delivery',
                'title'  =>'货到付款',
                'stitle' =>'货到付款',
                'icon'   =>'delivery.png',
            ]
        ];
        if($key == 'all'){
            return $pay_channel;
        }else if(empty($key) || $key == ''){
            return $pay_channel['delivery'];
        }else{
            return $pay_channel[$key];
        }
    }
    
    
}