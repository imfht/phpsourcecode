<?php

/**
 * wch_native.php  UTF8
 * @author 微彩虹 weicaihong 微彩虹--专注商城与微信的整合，轻松实现B2C网站+移动网站+微信商城数据同步
 * @date 2015-03-1
 * @copyright http://www.weicaihong.com
 */


if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/wch_native.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'wch_native_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = '微彩虹';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.weicaihong.com';

    /* 版本号 */
    $modules[$i]['version'] = '2.0';

    /* 配置信息 */
    $modules[$i]['config']  = array(
    );

    return;
}

/**
 * 类
 */
class wch_native
{

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    var $parameters; //cft 参数
    var $payments; //配置信息

    function __construct()
    {
        $this->wch_native_pay();
    }

    function wch_native_pay()
    {
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {

        if (!defined('EC_CHARSET'))
        {
            $charset = 'UTF-8';
        }
        else
        {
            $charset = EC_CHARSET;
        }

        $charset = strtoupper($charset);

        //初始化
        $body = '交易号：'.$order['order_sn'];
        $out_trade_no = $order['order_sn'] .'-'. $order['log_id'];
        $total_fee = $order['order_amount'];

        $wch_data = array(
            'body'=>$body,
            'out_trade_no'=>$out_trade_no,
            'total_fee'=>$total_fee,
            'ip'=>real_ip(),
            );

        $wxpay_api = 'http://mp.weicaihong.com/index.php/open/wxpay/native2'.'?wchToken='.md5(appId);

        $wch_json = $this->curl_post($wxpay_api, $wch_data);

        $wch_nav = json_decode($wch_json,TRUE);


        if($wch_nav['errmsg'] == 'ok')
        {
            $nav_url = $wch_nav['native_url'];
        }


        $qr_url = 'http://mp.weicaihong.com/index.php/open/create_qrcode?url='.$nav_url;

        $button = '<img src="'.$qr_url.'">';

        return $button;


    }

    /**
     * 响应操作
     */
    function callback()
    {
        if($_GET['status'] == 1){
            return true;
        }
        else{
            return false;
        }
    }


    function curl_post($url,$data,$proxy='',$proxystatus='',$ref_url='')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($proxystatus == 'true') {
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        if(!empty($ref_url)){
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
            curl_setopt($ch, CURLOPT_REFERER, $ref_url);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        ob_start();
        return curl_exec ($ch);
        ob_end_clean();
        curl_close ($ch);
        unset($ch);
    }

}