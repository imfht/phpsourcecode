<?php
function index()
{
	global $request;	

	if(!$_SESSION[TB_PREFIX.'pay_orderId']&&!$request['orderId'])exit();
}
function alipay()
{
	if(!$_SESSION[TB_PREFIX.'pay_orderId']&&!$request['orderId'])exit();
	
	require_once(ABSPATH."/inc/pay/alipay/alipay.config.php");
	require_once(ABSPATH."/inc/pay/alipay/lib/alipay_service.class.php");
	
	$price   = @explode('<@>',$_SESSION[TB_PREFIX.'pay_price']);
	
	
	$out_trade_no = $_SESSION[TB_PREFIX.'pay_orderId'];
	$subject      = strtr($_SESSION[TB_PREFIX.'pay_subject'],'<@>',' + ');
	$body         = strtr($_SESSION[TB_PREFIX.'pay_body'],'<@>','、');
	$total_fee    = array_sum($price);
	
	$show_url     = PAY_SHOW_URL;
		
	//构造要请求的参数数组
	$parameter = array(
			"service"			=> "create_direct_pay_by_user",
			"payment_type"		=> "1",
			
			"partner"			=> trim($aliapy_config['partner']),
			"_input_charset"	=> trim(strtolower($aliapy_config['input_charset'])),
			"seller_email"		=> trim($aliapy_config['seller_email']),
			"return_url"		=> trim($aliapy_config['return_url']),
			"notify_url"		=> trim($aliapy_config['notify_url']),
			
			"out_trade_no"		=> $out_trade_no,
			"subject"			=> $subject,
			"body"				=> $body,
			"total_fee"			=> $total_fee,
			
			"paymethod"			=> $paymethod,
			"defaultbank"		=> $defaultbank,
			
			"anti_phishing_key"	=> $anti_phishing_key,
			"exter_invoke_ip"	=> $exter_invoke_ip,
			
			"show_url"			=> $show_url,
			"extra_common_param"=> $extra_common_param,
			
			"royalty_type"		=> $royalty_type,
			"royalty_parameters"=> $royalty_parameters
	);
	
	//构造即时到帐接口
	$alipayService = new AlipayService($aliapy_config);
	$html_text = $alipayService->create_direct_pay_by_user($parameter);
	echo $html_text;
}
function tenpay()
{
	if(!$_SESSION[TB_PREFIX.'pay_orderId']&&!$request['orderId'])exit();
	
	require_once(ABSPATH."/inc/pay/tenpay/classes/PayRequestHandler.class.php");

	$bargainor_id = PAY_PARTNER_TEN;

	$key = PAY_KEY_TEN;

	$return_url = "http://".WEBURL."/inc/pay/tenpay.php";
	
	$strDate = date("Ymd");
	$strTime = date("His");
	
	

	$sp_billno = $_SESSION[TB_PREFIX.'pay_orderId'];

	$transaction_id = $bargainor_id . $strDate . $strReq;
	
    $price     = @explode('<@>',$_SESSION[TB_PREFIX.'pay_price']);
	$total_fee = array_sum($price)*100;

	$desc = strtr($_SESSION[TB_PREFIX.'pay_subject'],'<@>',' + ');
	$desc = iconv("UTF-8","GB2312",$desc);
		
	/* 创建支付请求对象 */
	$reqHandler = new PayRequestHandler();
	$reqHandler->init();
	$reqHandler->setKey($key);
	
	//----------------------------------------
	//设置支付参数
	//----------------------------------------
	$reqHandler->setParameter("bargainor_id", $bargainor_id);			//商户号
	$reqHandler->setParameter("sp_billno", $sp_billno);					//商户订单号
	$reqHandler->setParameter("transaction_id", $transaction_id);		//财付通交易单号
	$reqHandler->setParameter("total_fee", $total_fee);					//商品总金额,以分为单位
	$reqHandler->setParameter("return_url", $return_url);				//返回处理地址
	$reqHandler->setParameter("desc", $desc);	//商品名称
	
	//用户ip,测试环境时不要加这个ip参数，正式环境再加此参数
	$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
	//请求的URL
	$reqUrl = $reqHandler->getRequestURL();
	redirect($reqUrl);
}

//货到付款接口 （预留）
function hdfk()
{
	global $db;	
}
?>