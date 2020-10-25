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
<?php require_once('../../Connections/localhost.php'); ?>
<?php




//	检查支付宝交易号码是否正确，如果不正确的话，那么说明支付宝返回错了，也需要提示用户


/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
$error="";
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php

try{
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result){
	//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号
 	$out_trade_no = $_GET['out_trade_no'];

	//支付宝交易号
 	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];


//				记录进入订单处理日志
			mysql_select_db($database_localhost, $localhost);
			$new_order_log_sql="insert into pay_log(result,order_sn)values('".serialize($_GET)."','".$colname_order."')";
			if(!mysql_query($new_order_log_sql)){
				throw new Exception("系统错误，请稍后重试！".mysql_error());
			}
			

    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		
			//	根据支付宝返回回来的订单序列号找到相应的订单
			$colname_order = "-1";
			if (isset($_GET['out_trade_no'])) {
			  $colname_order = (get_magic_quotes_gpc()) ? $_GET['out_trade_no'] : addslashes($_GET['out_trade_no']);
			}
			mysql_select_db($database_localhost, $localhost);
			$query_order = sprintf("SELECT * FROM orders WHERE sn = '%s'", $colname_order);
			$order = mysql_query($query_order, $localhost);
			if(!$order){
				throw new Exception("订单序列号错误，请稍后重试！");
			} 
  			
			$row_order = mysql_fetch_assoc($order);
			$totalRows_order = mysql_num_rows($order);


			if($totalRows_order==0){
			
				//如果不能找到的话，说明订单不存在，或是参数错误	，这个时候需要给出提示信息
 				throw new Exception("订单不存在，请返回订单列表重试！");
			}
			if($order ['order_status']!=0){
				throw new Exception("订单已经支付或被撤销，请勿重复支付！");
			} 
 
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			
			
			//		将这个订单的状态设置为已经支付状态
			$update_order_status_sql="update orders set order_status=".ORDER_STATUS_PAID." , pay_at='".date('Y-m-d H:i:s')."' WHERE sn='".$colname_order."'";
			$update_order_status_query=mysql_query($update_order_status_sql);
			if(!$update_order_status_query){
				throw new Exception("更新订单状态错误，请稍后重试！".$update_order_status_sql);
			} 
			
			//		循环所有的产品，将他们的数量-1
			mysql_select_db($database_localhost, $localhost);
			$query_products = "SELECT * FROM order_item WHERE order_id =".$row_order ['id'];
			$products = mysql_query($query_products, $localhost);
			
			$totalRows_products = mysql_num_rows($products);
			while($row_products = mysql_fetch_assoc($products)){
 				$update_product_store_num_sql="update product set store_num=store_num-1,sold_num=sold_num+1 where id=".$row_products['product_id'];
				if(!mysql_query($update_product_store_num_sql)){
					throw new Exception("更新库存错误，请稍后重试！");
				}	
			}
			
			  
$order_log_sql="insert into order_log(order_id,message)values('".$row_order ['id']."','订单支付成功')";
mysql_query($order_log_sql, $localhost);

     }
    else {
		throw new Exception("未知支付状态：".$_GET['trade_status']);
      	//echo "trade_status=".$_GET['trade_status'];
    }
		
}else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
   throw new Exception("无法验证返回，请稍后重试！");
}

}catch(Exception $ex){
	$error=$ex->getMessage();
}
?>
<title>支付宝支付结果</title>
<style>
button,input{
	display:none;
}
</style>
</head>
<body style="margin:0px;">
<?php include_once($_SERVER['DOCUMENT_ROOT'].'/widget/top_full_nav.php'); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT'].'/widget/logo_search_cart.php'); ?>
<div align="center">
<div style="-webkit-border-radius:10px;-moz-border-radius:10px;margin:auto 0;height:294px;width:593px;border:2px solid #75a8d3;">
<table width="593" height="294" border="0" align="center" cellpadding="0">
<tr>
<td height="160"><div align="center"><span class="STYLE1"><?php if($error==''){ ?>恭喜您，支付成功!<?php  }else{ echo $error; }?> </span></div></td>
</tr>
<?php if($error==''){ ?>
<tr>
<td><div align="center" class="STYLE2">您现在可以<a href="/user/index.php?path=order/detail.php?sn=<?php echo $row_order ['sn'];?>">查看订单状态</a>或是<a href="javascript:window.opener=null;window.open('','_self');window.close();">关闭本窗口</a></div></td>
</tr>
<tr>
<?php }?>
<td>&nbsp;</td>
</tr>
</table>
</div>
</div>
</body>
</html>