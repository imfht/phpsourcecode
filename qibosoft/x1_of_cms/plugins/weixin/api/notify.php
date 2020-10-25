<?php
//ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once dirname(__FILE__).'/'."lib/WxPay.Api.php";
require_once dirname(__FILE__).'/'.'lib/WxPay.Notify.php';
require_once dirname(__FILE__).'/'.'log.php';

//初始化日志
$logHandler= new CLogFileHandler(ROOT_PATH.date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);


class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		
        global $pay_end_data;
        $pay_end_data = $data;
		//olpay_end($data['out_trade_no'],$data['attach']);
		//olpay_end($data['out_trade_no']);	//支付成功后，执行相关程序操作,transaction_id是微信的订单号，out_trade_no是商城的订单号，他们是不一样的。
		return true;
	}
}
Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
