<?php
/*
	*功能：设置财付通帐户有关信息及返回接收路径
	*日期：2010-07-7
	'备注：
	'该部分代码只是提供一个支付案例，方便商户商户使用及测试，可以帮助开发人员快速入门并掌握开发技能；
	'对于具有WEB程序开发背景的技术开发人员，可以另外开发接口。
*/

//设置财付通App-id: 财付通App注册时，由财付通分配
$appid = "0000000202";    

//签名密钥: 开发者注册时，由财付通分配
$key = "33182060802321342830012253725443";      

// 设置通知url：接收财付通后台通知的URL，用户在财付通完成支付后，财付通会回调此URL，向财付通APP反馈支付结果。
// 此URL可能会被多次回调，请正确处理，避免业务逻辑被多次触发。需给绝对路径，例如：http://wap.isv.com/notify.asp
$notify_url = "http://localhost/tenpay/examples/notify_url.php";  

// 设置返回url：用户完成支付后跳转的URL，财付通APP应在此页面上给出提示信息，引导用户完成支付后的操作。
// 财付通APP不应在此页面内做发货等业务操作，避免用户反复刷新页面导致多次触发业务逻辑造成不必要的损失。
// 需给绝对路径，例如：http://wap.isv.com/after_pay.asp，通过该路径直接将支付结果以Get的方式返回
$return_url = "http://localhost/tenpay/examples/return_url.php"; 
//在本地开发还是在服务端
$is_dev = false;

function get_tenpay_config_by_achPay_id($ach_pay_id='')
{
	$tenpay_key_info = get_private_security_key();
	$curlGet = '?tenpay_key='.urlencode($tenpay_key_info["key"]).'&ach_pay_id='.urlencode($ach_pay_id);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:".$tenpay_key_info["port"].$tenpay_key_info["url"].$curlGet);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	if (empty($data)) {
		$tenpay_key_error = array("type" => "error", "message" => "密匙匹配失败");
		echo json_encode($tenpay_key_error);
		exit();
	}else{
		try {
			$tenpay_config = json_decode($data);
			return $tenpay_config;
		} catch (Exception $e) {
			$file_error = array("type" => "error", "message" => $data);
			echo json_encode($file_error);
			exit();
		}
	}
};
function get_private_security_key()
{
	try {
		$string = file_get_contents("/root/O2O/_include/.tenpay.key.json");
		if(empty($string)){
			$string = file_get_contents("E:/Git/O2O/_include/.tenpay.key.json");
			$GLOBALS["is_dev"] = true;
		}else{
			$GLOBALS["is_dev"] = false;
		}
		$tenpay_key_info = json_decode($string,true);
	} catch (Exception $e) {
		$file_error = array("type" => "error", "message" => "密匙文件无效");
		echo json_encode($file_error);
		exit();
	}
	return $tenpay_key_info;
};
?>