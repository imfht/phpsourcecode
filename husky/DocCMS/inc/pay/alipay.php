<?php 
require_once("../../config/doc-config.php");
require_once("../class.database.php");
require_once("alipay/alipay.config.php");
require_once("alipay/lib/alipay_notify.class.php");

global $db;
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功

$out_trade_no	= $_GET['out_trade_no'];	//获取订单号
$trade_no		= $_GET['trade_no'];		//获取支付宝交易号
$total_fee		= $_GET['total_fee'];		//获取总价格

if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {

}
else {
  echo "trade_status=".$_GET['trade_status'];
}
$_SESSION[TB_PREFIX.'pay_orderId']  ='';
$_SESSION[TB_PREFIX.'pay_subject']  ='';
$_SESSION[TB_PREFIX.'pay_body']     ='';
$_SESSION[TB_PREFIX.'pay_price']    ='';
$sql ="UPDATE ".TB_PREFIX."product_order SET ispay= 1 ,payprice= ".($total_fee)." WHERE orderId = '".$out_trade_no."'";
$db->query($sql);
echo "<script>window.location.href='".PAY_SHOW_URL."'</script>";
exit;
}
else {

echo "验证失败";
exit;
}
?>