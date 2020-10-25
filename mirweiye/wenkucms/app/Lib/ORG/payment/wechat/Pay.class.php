<?php
namespace Org\Payment\Wechat;

require_once "sdk/WxPay.Api.php";
require_once "sdk/WxPay.JsApiPay.php";
require_once "sdk/WxPay.Config.php";
require_once "sdk/WxPay.Notify.php";

class Pay
{
	public function __construct($config)
	{
		$this->config = new \WxPayConfig();
		# 通知接口
    	$config['notify'] =  'http://'.$_SERVER['HTTP_HOST'] . '/system/wechat';
    	# 证书，仅退款、撤销订单时需要，这里不加了
    	$config['ssl'] = array
    	(
    		'cert' => '',
    		'key' => '',
    	);

		$this->config->set($config['appid'], $config['appsecret'], $config['merchant'], $config['notify'], $config['key'], $config['ssl']);
	}

	/**
	 * 获取openid
	 */
	public function getOpenid()
	{
		//return 'ofeExuBhSuMNdSg-XKvbRFte2HoE';
		$tools = new \JsApiPay($this->config);
		return $tools->GetOpenid();
	}

	/**
	 * 通知
	 */
	public function notify()
	{
		$callback = new Callback();
		$callback->Handle($this->config, false);
	}

	/**
	 * 获取统一下单的基本信息
	 */
	public function order($orderid, $openid, $name, $num)
	{
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($name);
		$input->SetAttach($name);
		$input->SetOut_trade_no($orderid);
		$input->SetTotal_fee($num);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		//$input->SetGoods_tag($name);
		$input->SetNotify_url($this->config->GetNotifyUrl());
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openid);
		$order = \WxPayApi::unifiedOrder($this->config, $input);
		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		//print_r($order);die;
		$tools = new \JsApiPay($this->config);
		$jsApiParameters = $tools->GetJsApiParameters($order);

		return $jsApiParameters;
	}


	/**
	 * 获取js
	 */
	public function jsapi($url, $orderid, $openid, $name, $num)
	{
		$info = $this->order($orderid, $openid, $name, $num);
		$html = '<script type="text/javascript">
		function jsApiCall()
		{
			WeixinJSBridge.invoke(
				"getBrandWCPayRequest",
				'.$info.',
				function(res){
					//WeixinJSBridge.log(res.err_msg);
					if(res.err_msg == "get_brand_wcpay_request:ok")
					{
						location.href = "'.$url.'";
					} else {
						alert(res.err_code+res.err_desc+res.err_msg);
					}
				}
			);
		}

		function callpay()
		{
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent("WeixinJSBridgeReady", jsApiCall); 
			        document.attachEvent("onWeixinJSBridgeReady", jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}
		callpay();
		</script>';

		return $html;
	}
}

class Callback extends \WxPayNotify
{
	//重写回调处理函数
	/**
	 * @param WxPayNotifyResults $data 回调解释出的参数
	 * @param WxPayConfigInterface $config
	 * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
	 * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
	 */
	public function NotifyProcess($objData, $config, &$msg)
	{
		$data = $objData->GetValues();
		//TODO 1、进行参数校验
		if(!array_key_exists("return_code", $data) 
			||(array_key_exists("return_code", $data) && $data['return_code'] != "SUCCESS")) {
			//TODO失败,不是支付成功的通知
			//如果有需要可以做失败时候的一些清理处理，并且做一些监控
			$msg = "异常异常";
			return false;
		}
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}

		//TODO 2、进行签名验证
		try {
			$checkResult = $objData->CheckSign($config);
			if($checkResult == false){
				//签名错误
				//Log::ERROR("签名错误...");
				return false;
			}
		} catch(Exception $e) {
			//Log::ERROR(json_encode($e));
		}

		//TODO 3、处理业务逻辑
		$notfiyOutput = array();
		$recharge = D('recharge');
        $info['sn'] = $data["out_trade_no"];
        $info['cash'] = $data['cash_fee'];

		$recharge->succPay($info);
		
		
		//查询订单，判断订单真实性
		/*
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		*/
		return true;
	}
}