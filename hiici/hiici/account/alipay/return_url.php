<?php

require_once('../../inc/config.php');
require_once('../../inc/func.php');		//通用函数 

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");

?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {

	$out_trade_no = $_GET['out_trade_no'];
	$trade_no = $_GET['trade_no'];
	$trade_status = $_GET['trade_status'];

	if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {

	} 
	else {
		echo "trade_status=".$_GET['trade_status'];
	}

	echo "充值成功!^_^<br />";
	echo "trade_no=".$trade_no;
	echo "<script>location='".s_url('?c=account&a=index')."'</script>";
}
else {
	echo "验证失败!^_^";
}
?>
	<title>支付宝纯担保交易接口</title>
	</head>
    <body>
    </body>
</html>
