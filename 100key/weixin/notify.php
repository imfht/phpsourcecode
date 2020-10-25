<?php
//后台通知页面
include_once('Weixinpay.php');
$notify = new Weixinpay();

//存储微信的回调
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
$notify->saveData($xml);//把返回的数据转换成数组

//验证签名，并回应微信。
//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
//尽可能提高通知的成功率，但微信不保证通知最终能成功。
if($notify->checkSign() == FALSE){
    $notify->setReturnParameter("return_code","FAIL");//返回状态码
	$notify->setReturnParameter("return_msg","签名失败");//返回信息
}else{
	$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
}

if($notify->checkSign() == TRUE)
{
	if ($notify->data["return_code"] == "FAIL") {
		//此处应该更新一下订单状态，商户自行增删操作
	}
	elseif($notify->data["result_code"] == "FAIL"){
		//此处应该更新一下订单状态，商户自行增删操作
	}
	elseif($notify->data["result_code"] == "SUCCESS" && $notify->data["return_code"] == "SUCCESS"){
		//支付成功,更新订单状态
        $order_number=$notify->data["out_trade_no"];//订单号
    	$transaction_id=$notify->data["transaction_id"];//微信订单号
		$total_fee=(int)(($notify->data["total_fee"])/100);//微信订单总金额
        //支付成功，然后更新订单状态
	}
}
?>