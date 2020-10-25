<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/../third_party/wxpay/WxPay.Api.php';
require_once __DIR__ . '/../helpers/cloud.php';

use \LeanCloud\Engine\LeanEngine;
use \LeanCloud\Engine\Cloud;
use \LeanCloud\Client;
use \LeanCloud\Storage\CookieStorage;
/*
 * Define cloud functions and hooks on LeanCloud
 */

// /1.1/functions/sayHello
Cloud::define("pay", function($params, $user) {
	// var_dump($user);
	$openid = $user->get('authData')["lc_weapp"]["openid"];
	// 		初始化值对象
	$input = new WxPayUnifiedOrder();
	// 		文档提及的参数规范：商家名称-销售商品类目
	$input->SetBody($params['body']);
	// 		订单号应该是由小程序端传给服务端的，在用户下单时即生成，demo中取值是一个生成的时间戳
	$input->SetOut_trade_no($params['tradeNo']);
	// 		费用应该是由小程序端传给服务端的，在用户下单时告知服务端应付金额，demo中取值是1，即1分钱
	$input->SetTotal_fee($params['totalFee']);
	$input->SetNotify_url("https://laeser.leanapp.cn/WXPay/notify");
	$input->SetTrade_type("JSAPI");
	// 		由小程序端传给服务端
	$input->SetOpenid($openid);
	// 		向微信统一下单，并返回order，它是一个array数组
	$order = WxPayApi::unifiedOrder($input);
	// 		json化返回给小程序端
	header("Content-Type: application/json");
	// var_dump($order);
	return getJsApiParameters($order);
	// return "hello {$params['name']}";
});

function getJsApiParameters($UnifiedOrderResult) {
	if(!array_key_exists("appid", $UnifiedOrderResult)
	|| !array_key_exists("prepay_id", $UnifiedOrderResult)
	|| $UnifiedOrderResult['prepay_id'] == "")
	{
		throw new WxPayException("参数错误");
	}
	$jsapi = new WxPayJsApiPay();
	$jsapi->SetAppid($UnifiedOrderResult["appid"]);
	$timeStamp = time();
	$jsapi->SetTimeStamp("$timeStamp");
	$jsapi->SetNonceStr(WxPayApi::getNonceStr());
	$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
	$jsapi->SetSignType("MD5");
	$jsapi->SetPaySign($jsapi->MakeSign());
	$parameters = json_encode($jsapi->GetValues());
	return $parameters;
}

class CIEngine extends LeanEngine {
	function __invoke() {
		$this->dispatch($_SERVER['REQUEST_METHOD'],
			$_SERVER['REQUEST_URI']);
	}
}

$hook['pre_system'] = function() {
	// 参数依次为 AppId, AppKey, MasterKey
	Client::initialize("7tm1OFlNlmLFukegUhmm4uDU-gzGzoHsz", "XG4FRumQWJ7mNkFIral0ttvj" ,"bx7mYx7UTLW35cEawr8cxCK2");
	Client::useMasterKey(true);
	Client::setStorage(new CookieStorage());
	$engine = new CIEngine();
	// 以下是核心语句，直接像使用函数那样在对象上调用
	$engine();
};
