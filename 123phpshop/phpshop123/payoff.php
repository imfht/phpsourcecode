<?php
/**
 * 123PHPSHOP
 * ============================================================================
 * 版权所有 2015 上海序程信息科技有限公司，并保留所有权利。
 * 网站地址: http://www.123PHPSHOP.com；
 * ----------------------------------------------------------------------------
 * 这是一个免费的软件。您可以在商业目的和非商业目的地前提下对程序除本声明之外的
 * 代码进行修改和使用；您可以对程序代码以任何形式任何目的的再发布，但一定请保留
 * 本声明和上海序程信息科技有限公司的联系方式！本软件中使用到的第三方代码版权属
 * 于原公司所有。上海序程信息科技有限公司拥有对本声明和123PHPSHOP软件使用的最终
 * 解释权！
 * ============================================================================
 *  作者:	123PHPSHOP团队
 *  手机:	13391334121
 *  邮箱:	service@123phpshop.com
 */
?>
<?php require_once('Connections/localhost.php'); ?>
<?php

// 通过s3来获取订单， 。通过订单的序列号来获取订单的详细情况。
$colname_order = "-1";
if (isset($_GET['order_sn'])) {
  $colname_order = (get_magic_quotes_gpc()) ? $_GET['order_sn'] : addslashes($_GET['order_sn']);
}
mysql_select_db($database_localhost, $localhost);
$query_order = sprintf("SELECT * FROM orders WHERE sn = '%s' and is_delete=0 ", $colname_order);
$order = mysql_query($query_order, $localhost) or die(mysql_error());
$row_order = mysql_fetch_assoc($order);
$totalRows_order = mysql_num_rows($order);

//	如果没有办法找到这个订单的话,那么告知
if($totalRows_order==0){
		$url="/";
	 header("Location: " . $url );
}
//	如然后检查订单的消息状态，如果说这个订单已经被支付的话，那么需要跳转
if($row_order['order_status']!=0){
	$url="/";
	 header("Location: " . $url );
}

mysql_select_db($database_localhost, $localhost);
$query_pay_method = "SELECT * FROM pay_method WHERE is_activated = 1";
$pay_method = mysql_query($query_pay_method, $localhost) or die(mysql_error());
$row_pay_method = mysql_fetch_assoc($pay_method);
$totalRows_pay_method = mysql_num_rows($pay_method);
$consignee_id=0;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>请选择支付方式</title>
<style type="text/css">
<!--
body {
	background-color: #F1F2F7;
	font-family:Arial,Verdana,"宋体"
}
#zhifu_white{
	background-color:#FFFFFF;
	width:990px;
	margin:10px auto;
}
.need_pay{
	color:#FF5D5B;
	font-weight:bold;
	font-size:18px;
margin:0px 3px;
}
-->
</style></head>

<body style="margin:0;">
<?php include_once('widget/top_full_nav.php'); ?>
<?php include_once('widget/cashdesk.php'); ?>
<table width="990" height="46" border="0" align="center" cellpadding="0" cellspacing="0" style="margin:10px auto;">
  <tr>
    <td width="50%" height="26" style="font-size:14px;">订单提交成功，请尽快付款，订单号：<strong><?php echo $row_order['sn']; ?></strong></td>
    <td height="26"><div align="right" style="font-size:12px;">应付金额<span class="need_pay"><?php echo $row_order['should_paid']; ?></span>元</div></td>
  </tr>
  <tr>
    <td style="font-size:12px;" height="20"> 请您在添加订单后24小时内完成支付</td>
    <td  height="20"><div align="right" ><a href="/" style="font-size:12px;text-decoration:none;color:#2ea7e7;visibility:hidden;">订单详情</a></div></td>
  </tr>
</table>
<div id="zhifu_white">
    <form id="alipayment" name="alipayment" method="post" action="/payment/alipay/alipayapi.php">

  <?php $checked=true;do { 
   ?>
      <table width="990" height="46" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
        <tr>
          <td width="215"><label>
            <input name="pay_method" type="radio" value="<?php echo $row_pay_method['id']; ?>" <?php if($checked){ ?>checked<?php }?> />
            </label>
          <?php echo $row_pay_method['name']; ?></td>
        <td width="641"></td>
        <td width="74" colspan="2"><p align="right">
          <input style="margin-right:10px;border-radius:4px;width:106px;height:34px;border:1px solid #54bef5;background-color:#54bef5;color:#ffffff;font-size:12px;" type="submit" name="Submit" value="立即支付" />
          </p>    </td>
      </tr>
      </table>
      <input name="WIDbody" type="hidden" value="订单及时到账支付" size="30" />
      <input name="WIDshow_url" type="hidden" value="http://<?php echo  $_SERVER['SERVER_NAME'];?>" size="30" />
      <input name="WIDout_trade_no" type="hidden" value="<?php echo $row_order['sn']; ?>" size="30" />
      <input name="WIDsubject"  type="hidden" value="订单号：<?php echo $row_order['sn']; ?>" size="30" />
      <input name="WIDtotal_fee"  type="hidden" value="<?php echo $row_order['should_paid']; ?>" size="30" />
    <?php $checked=false;} while ($row_pay_method = mysql_fetch_assoc($pay_method)); ?>
	        </form>

	</div>
 <hr width="90%" style="border:none;border-bottom:1px solid #dddddd;position:absolute;bottom:40px;left:5%;"/>
 <div style="color:#666666;width:90%;left:5%;position:absolute;bottom:10px;text-align:center;">
   <div align="center">Copyright © 2015 上海序程信息科技有限公司www.123phpshop.com 版权所有</div>
 </div>
</body>
</html>
