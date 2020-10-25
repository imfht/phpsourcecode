<?php
require_once ("../../config/doc-config.php");
require_once ("../class.database.php");
require_once ("tenpay/classes/PayResponseHandler.class.php");

global $db;
/* 密钥 */
$key = PAY_KEY_TEN;

/* 创建支付应答对象 */
$resHandler = new PayResponseHandler();
$resHandler->setKey($key);
//判断签名
if($resHandler->isTenpaySign()) {
	//交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");
	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");
	if( "0" == $pay_result ) {
		$_SESSION[TB_PREFIX.'pay_orderId']  ='';
		$_SESSION[TB_PREFIX.'pay_subject']  ='';
		$_SESSION[TB_PREFIX.'pay_body']     ='';
		$_SESSION[TB_PREFIX.'pay_price']    ='';

		//调用doShow, 打印meta值跟js代码,告诉财付通处理成功,并在用户浏览器显示$show页面.
		$sql ="UPDATE ".TB_PREFIX."product_order SET ispay= 1 ,payprice= ".($total_fee%100)." WHERE orderId = '".$transaction_id."'";
		$db->query($sql);
		
		$show = PAY_SHOW_URL_TEN;
		$resHandler->doShow($show);
	
	} else {
		//当做不成功处理
		echo "<br/>" . "支付失败" . "<br/>";
	}
	
} else {
	echo "<br/>" . "认证签名失败" . "<br/>";
}
?>