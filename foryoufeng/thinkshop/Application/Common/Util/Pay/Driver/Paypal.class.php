<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: ijry <ijry@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Util\Pay\Driver;
/**
 * Paypal驱动
 */
class Paypal extends \Think\Pay\Pay {
    protected $gateway = 'https://www.paypal.com/cgi-bin/webscr';
    protected $config = array(
        'business' => ''
    );

    public function check() {
        if (!$this->config['business']) {
            E("贝宝设置有误！");
        }
        return true;
    }

    public function buildRequestForm($pay_data) {
        $param = array(
            'cmd' => '_xclick',
            'charset' => 'utf-8',
            'business' => $this->config['business'],
            'currency_code' => 'USD',
            'notify_url' => $this->config['notify_url'],
            'return' => $this->config['return_url'],
            'invoice' => $pay_data['out_trade_no'],
            'item_name' => $pay_data['title'],
            'amount' => $pay_data['money'],
            'no_note' => 1,
            'no_shipping' => 1
        );
        $sHtml = $this->_buildForm($param, $this->gateway);
        return $sHtml;
    }

    public function verifyNotify($notify) {
        if (empty($notify['txn_id']))
            return false;
        $tmpAr = array_merge($notify, array("cmd" => "_notify-validate"));

        $ppResponseAr = $this->fsockOpen($this->gateway, 0, $tmpAr);
        if ((strcmp($ppResponseAr, "VERIFIED") == 0) && $notify['receiver_email'] == $this->config['business']) {
            $info = array();
            //支付状态
            $info['status'] = $notify['payment_status'] == 'Completed' ? true : false;
            $info['money'] = $notify['mc_gross'];
            $info['out_trade_no'] = $notify['invoice'];
            $this->info = $info;
            return true;
        }
        return false;
    }
}
