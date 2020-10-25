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
 /* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知

 */

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php"); 

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();


if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代

	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
	
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
	//商户订单号

	$out_trade_no = $_POST['out_trade_no'];

	//支付宝交易号
 	$trade_no = $_POST['trade_no'];

	//交易状态
	$trade_status = $_POST['trade_status'];

  	
//				记录进入订单处理日志
	$new_order_log_sql="insert into pay_log(result,order_sn)values('".serialize($_POST)."','".$colname_order."')";
	if(!mysql_query($new_order_log_sql)){
 		phpshop_log("错误：订单log错误".$query_order);
		echo "fail";return;
	}
			
    if($_POST['trade_status'] == 'TRADE_FINISHED') {
		//	根据支付宝返回回来的订单序列号找到相应的订单
			$colname_order = "-1";
			if (isset($_POST['out_trade_no'])) {
			  $colname_order = (get_magic_quotes_gpc()) ? $_POST['out_trade_no'] : addslashes($_POST['out_trade_no']);
			}
			
			mysql_select_db($database_localhost, $localhost);
			$query_order = sprintf("SELECT * FROM orders WHERE sn = '%s'", $colname_order);
			$order = mysql_query($query_order, $localhost);
			if(!$order){
				phpshop_log("订单查询错误".$query_order);
				echo "fail";return;
			} 
  			
			$row_order = mysql_fetch_assoc($order);
			$totalRows_order = mysql_num_rows($order);
 			
			//如果不能找到的话，说明订单不存在，或是参数错误	，这个时候需要给出提示信息

			if($totalRows_order==0){
 			 	phpshop_log("找不到订单".$query_order);
				echo "fail";return;
			}
			if($order ['order_status']!=0){
				phpshop_log("错误：订单状态已经支付或是撤销".$query_order);
				echo "fail";return;
			} 
 
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			
			
			//		将这个订单的状态设置为已经支付状态
			$update_order_status_sql="update orders set order_status=".ORDER_STATUS_PAID." , pay_at='".date('Y-m-d H:i:s')."' WHERE sn='".$colname_order."'";			
			$update_order_status_query=mysql_query($update_order_status_sql);
			if(!$update_order_status_query){
				phpshop_log("错误：更新订单状态查询失败".$query_order);
				echo "fail";return;
			} 
			
			//		循环所有的产品，将他们的数量-1
			mysql_select_db($database_localhost, $localhost);
			$query_products = "SELECT * FROM order_item WHERE order_id =".$row_order ['id'];
			$products = mysql_query($query_products, $localhost) or die(mysql_error());
			
			$totalRows_products = mysql_num_rows($products);
			while($row_products = mysql_fetch_assoc($products)){
  				$update_product_store_num_sql="update product set store_num=store_num-1,sold_num=sold_num+1 where id=".$row_products['product_id'];
				if(!mysql_query($update_product_store_num_sql)){
					phpshop_log("错误：更新订单产品库存和销售量查询失败".$query_order);
					echo "fail";return;
				}	
			}
			
			  
		$order_log_sql="insert into order_log(order_id,message)values('".$row_order ['id']."','订单支付确认')";
		mysql_query($order_log_sql, $localhost);

    }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
	
	
		//判断该笔订单是否在商户网站中已经做过处理
		//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
		//如果有做过处理，不执行商户的业务程序
		
		//注意：
		//付款完成后，支付宝系统发送该交易状态通知
		//调试用，写文本函数记录程序运行情况是否正常
		//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		
				//	根据支付宝返回回来的订单序列号找到相应的订单
			$colname_order = "-1";
			if (isset($_POST['out_trade_no'])) {
			  $colname_order = (get_magic_quotes_gpc()) ? $_POST['out_trade_no'] : addslashes($_POST['out_trade_no']);
			}
			
			mysql_select_db($database_localhost, $localhost);
			$query_order = sprintf("SELECT * FROM orders WHERE sn = '%s'", $colname_order);
			$order = mysql_query($query_order, $localhost);
			if(!$order){
				phpshop_log("订单查询错误".$query_order);
				echo "fail";return;
			} 
  			
			$row_order = mysql_fetch_assoc($order);
			$totalRows_order = mysql_num_rows($order);
 			
			//如果不能找到的话，说明订单不存在，或是参数错误	，这个时候需要给出提示信息

			if($totalRows_order==0){
 			 	phpshop_log("找不到订单".$query_order);
				echo "fail";return;
			}
			if($order ['order_status']!=0){
				phpshop_log("错误：订单状态已经支付或是撤销".$query_order);
				echo "fail";return;
			} 
 
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			
			
			//		将这个订单的状态设置为已经支付状态
			$update_order_status_sql="update orders set order_status=".ORDER_STATUS_PAID." , pay_at='".date('Y-m-d H:i:s')."' WHERE sn='".$colname_order."'";			
			$update_order_status_query=mysql_query($update_order_status_sql);
			if(!$update_order_status_query){
				phpshop_log("错误：更新订单状态查询失败".$query_order);
				echo "fail";return;
			} 
			
			//		循环所有的产品，将他们的数量-1
			mysql_select_db($database_localhost, $localhost);
			$query_products = "SELECT * FROM order_item WHERE order_id =".$row_order ['id'];
			$products = mysql_query($query_products, $localhost) or die(mysql_error());
			
			$totalRows_products = mysql_num_rows($products);
			while($row_products = mysql_fetch_assoc($products)){
  				$update_product_store_num_sql="update product set store_num=store_num-1,sold_num=sold_num+1 where id=".$row_products['product_id'];
				if(!mysql_query($update_product_store_num_sql)){
					phpshop_log("错误：更新订单产品库存和销售量查询失败".$query_order);
					echo "fail";return;
				}	
			}
			
				  
	$order_log_sql="insert into order_log(order_id,message)values('".$row_order ['id']."','".订单支付成功."')";
	mysql_query($order_log_sql, $localhost);

			phpshop_log("信息：订单更新成功！".$query_order);


	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
 	echo "success";		//请不要修改或删除

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 }
}else {
    //验证失败
		
     echo "fail";
 	phpshop_log("错误：验证失败！".$query_order);
   
 }
?>