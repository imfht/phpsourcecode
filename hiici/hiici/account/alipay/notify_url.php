<?php

require_once('../../inc/config.php');
require_once('../../inc/dt.php');		//数据库模块
require_once('../../inc/func.php');		//通用函数 

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {

	$out_trade_no = $_POST['out_trade_no'];
	$trade_no = $_POST['trade_no'];
	$trade_status = $_POST['trade_status'];


	if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {

		echo "success";	
	}
	else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {

		if (!dt_query_one("SELECT id FROM account_charge_log WHERE status = 0 AND id = $out_trade_no LIMIT 1")) die('success');

		//执行充值
		$a_c_log = dt_query_one("SELECT user_id, price FROM account_charge_log WHERE id = $out_trade_no");
		if (!$a_c_log) die('fail');

		if (!do_account_pay($a_c_log['user_id'], $a_c_log['price'], '账户充值', 1)) die('fail');

		$rs = dt_query("UPDATE account_charge_log SET status = 1 WHERE id = $out_trade_no");
		if (!$rs) die('fail');

		//通知发货
		$trade_no = $trade_no;
		$logistics_name = 'HIICI';
		$invoice_no = $out_trade_no;
		$transport_type = 'EXPRESS';

		$parameter = array(
			"service" => "send_goods_confirm_by_platform",
			"partner" => trim($alipay_config['partner']),
			"trade_no"	=> $trade_no,
			"logistics_name"	=> $logistics_name,
			"invoice_no"	=> $invoice_no,
			"transport_type"	=> $transport_type,
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);

		require_once("lib/alipay_submit.class.php");
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$alipaySubmit->buildRequestHttp($parameter);

		echo "success";
	}
	else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {

		echo "success";
	}
	else if($_POST['trade_status'] == 'TRADE_FINISHED') {

		if (!dt_query_one("SELECT id FROM account_charge_log WHERE status = 1 AND id = $out_trade_no LIMIT 1")) die('success');

		//更新状态-充值结束
		$rs = dt_query("UPDATE account_charge_log SET status = 2 WHERE id = $out_trade_no");
		if (!$rs) die('fail');

		echo "success";
	}
	else {
		echo "success";
	}
}
else {
	echo "fail";
}
?>
