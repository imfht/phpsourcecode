<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{

	
	/**
     * 微信 公众号jssdk支付
	 * 测试地址: http://bjy.cc/Home/Index/weixinpay_js
	 * 线上地址: http://bjy.dakaifa.net/Home/Index/weixinpay_js
     */
    public function weixinpay_js(){
        // 此处根据实际业务情况生成订单 然后拿着订单去支付

        // 用时间戳虚拟一个订单号  （请根据实际业务更改）
        $out_trade_no=time();
        // 组合url
        $url=U('Api/Weixinpay/pay',array('out_trade_no'=>$out_trade_no));
        // 前往支付
        redirect($url);
    }

}