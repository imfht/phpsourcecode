<?php
//---------------------------------------------------------
//生成支付请求串示例,用于生成支付请求
//---------------------------------------------------------

require_once ("classes/PayRequest.class.php");
require_once ("./tenpay_config.php");

$curDateTime = date("YmdHis");
/* 商家的定单号 */
$out_trade_no = $_REQUEST["out_trade_no"];
/*商家的财付通信息*/
$tenpay_config = get_tenpay_config_by_achPay_id($out_trade_no);

/* 创建支付请求对象 */
$reqHandler = new PayRequest($tenpay_config->key);

// 设置在沙箱中运行，正式环境请设置为false
$reqHandler->setInSandBox($is_dev);
//----------------------------------------
//以下业务参数名称参考开放平台sdk文档-PHP
//----------------------------------------

// 设置财付通appid: 财付通app注册时，由财付通分配
$reqHandler->setAppid($tenpay_config->appid);

// 设置商户系统订单号：财付通APP系统内部的订单号,32个字符内、可包含字母,确保在财付通APP系统唯一
$reqHandler->setParameter("out_trade_no", $out_trade_no);           

// 设置订单总金额，单位为分
$reqHandler->setParameter("total_fee", $_REQUEST["order_price"]);					

// 设置通知url：接收财付通后台通知的URL，用户在财付通完成支付后，财付通会回调此URL，向财付通APP反馈支付结果。
// 此URL可能会被多次回调，请正确处理，避免业务逻辑被多次触发。需给绝对路径，例如：http://wap.isv.com/notify.asp
$reqHandler->setParameter("notify_url", $notify_url);				

// 设置返回url：用户完成支付后跳转的URL，财付通APP应在此页面上给出提示信息，引导用户完成支付后的操作。
// 财付通APP不应在此页面内做发货等业务操作，避免用户反复刷新页面导致多次触发业务逻辑造成不必要的损失。
// 需给绝对路径，例如：http://wap.isv.com/after_pay.asp，通过该路径直接将支付结果以Get的方式返回
$reqHandler->setParameter("return_url", $return_url);				

// 设置商品名称:商品描述，会显示在财付通支付页面上
$reqHandler->setParameter("body", $_REQUEST["product_name"]);	            

// 设置用户客户端ip:用户IP，指用户浏览器端IP，不是财付通APP服务器IP
$reqHandler->setParameter("spbill_create_ip", $_REQUEST['user_ip']);
// **********************end*************************

//支付请求的URL
$reqUrl = $reqHandler->getURL();

echo $reqUrl;
?>
