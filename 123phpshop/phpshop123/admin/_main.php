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
<?php require_once('../Connections/localhost.php'); ?>
<?php
mysql_select_db($database_localhost, $localhost);
$query_orders = "SELECT count(*) as total_order FROM orders";
$orders = mysql_query($query_orders, $localhost) or die(mysql_error());
$row_orders = mysql_fetch_assoc($orders);
$totalRows_orders = $row_orders['total_order'];

mysql_select_db($database_localhost, $localhost);
$query_users = "SELECT * FROM `user`";
$users = mysql_query($query_users, $localhost) or die(mysql_error());
$row_users = mysql_fetch_assoc($users);
$totalRows_users = mysql_num_rows($users);


mysql_select_db($database_localhost, $localhost);
$query_comment = "SELECT count(*)  as total FROM product_comment where is_delete=0";
$comment = mysql_query($query_comment, $localhost) or die(mysql_error());
$row_comment = mysql_fetch_assoc($comment);
$totalRows_comment = $row_comment['total'];

mysql_select_db($database_localhost, $localhost);
$query_product_consult = "SELECT count(*)  as total FROM product_consult where is_delete=0";
$product_consult = mysql_query($query_product_consult, $localhost) or die(mysql_error());
$row_product_consult = mysql_fetch_assoc($product_consult);
$totalRows_product_consult= $row_product_consult['total'];


mysql_select_db($database_localhost, $localhost);
$query_unpaied = "SELECT count(*)  as total  FROM orders WHERE order_status = 0";
$unpaied = mysql_query($query_unpaied, $localhost) or die(mysql_error());
$row_unpaied = mysql_fetch_assoc($unpaied);
$totalRows_unpaied = $row_unpaied['total'];

mysql_select_db($database_localhost, $localhost);
$query_finished = "SELECT count(*)  as total  FROM orders   WHERE order_status = 300";
$finished = mysql_query($query_finished, $localhost) or die(mysql_error());
$row_finished = mysql_fetch_assoc($finished);
$totalRows_finished = $row_finished['total'];

mysql_select_db($database_localhost, $localhost);
$query_refunded = "SELECT count(*)  as total FROM orders  WHERE order_status = -300";
$refunded = mysql_query($query_refunded, $localhost) or die(mysql_error());
$row_refunded = mysql_fetch_assoc($refunded);
$totalRows_refunded = $row_refunded['total'];

mysql_select_db($database_localhost, $localhost);
$query_withdrawled = "SELECT count(*)  as total FROM orders  WHERE order_status = -100";
$withdrawled = mysql_query($query_withdrawled, $localhost) or die(mysql_error());
$row_withdrawled = mysql_fetch_assoc($withdrawled);
$totalRows_withdrawled = $row_withdrawled['total'];

mysql_select_db($database_localhost, $localhost);
$query_paid = "SELECT count(*)  as total FROM orders   WHERE order_status = 100";
$paid = mysql_query($query_paid, $localhost) or die(mysql_error());
$row_paid = mysql_fetch_assoc($paid);
$totalRows_paid = $row_paid['total'];

mysql_select_db($database_localhost, $localhost);
$query_returned = "SELECT count(*)  as total  FROM orders   WHERE order_status = -200";
$returned = mysql_query($query_returned, $localhost) or die(mysql_error());
$row_returned = mysql_fetch_assoc($returned);
$totalRows_returned = $row_returned['total'];

mysql_select_db($database_localhost, $localhost);
$query_recent_orders = "SELECT orders.*,user.username FROM orders inner join user on user.id=orders.user_id where orders.is_delete=0 ORDER BY orders.id DESC limit 5";
$recent_orders = mysql_query($query_recent_orders, $localhost) or die(mysql_error());
$row_recent_orders = mysql_fetch_assoc($recent_orders);
$totalRows_recent_orders = mysql_num_rows($recent_orders);

$query_total_sales = "SELECT sum('actual_pay') as total FROM orders";
$total_sales = mysql_query($query_total_sales, $localhost) or die(mysql_error());
$row_total_sales = mysql_fetch_assoc($total_sales);
$totalRows_total_sales = $row_total_sales['total'];

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
<!--
.STYLE1 {font-size: 30px}
.STYLE2 {
	color: #FFFFFF;
	font-size: 12px;
}
a{
	text-decoration:none;
	color:#000000;
}
-->
</style>
</head>

<body>
<table width="100%" height="48" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><span class="STYLE1">管理中心</span></td>
  </tr>
</table>
<br />
<table width="100%" height="148" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><div align="left">
      <table width="385" height="148" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="28" bgcolor="#1E91CF"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;">订单总数</span></td>
          </tr>
          <tr bgcolor="#279FE0">
            <td height="92"><div align="right" style="font-size:42px;color:white;padding-right:10px;"><?php echo $totalRows_orders;?></div></td>
          </tr>
          <tr>
            <td height="28" bgcolor="#3DA9E3"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;"><a href="/admin/order/index.php" style="color:#FFFFFF;">查看更多</a>...</span></td>
          </tr>
        </table>
    </div></td>
    <td><table width="385" height="148" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="28" bgcolor="#1E91CF"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;">用户总数</span></td>
      </tr>
      <tr bgcolor="#279FE0">
        <td height="92"><div align="right" style="font-size:42px;color:white;padding-right:10px;"><?php echo $totalRows_users;?></div></td>
      </tr>
      <tr>
        <td height="28" bgcolor="#3DA9E3"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;"><a href="/admin/users/index.php" style="color:#FFFFFF;">查看更多</a>...</span></td>
      </tr>
    </table></td>
    <td><div align="center">
      <table width="385" height="148" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td height="28" bgcolor="#1E91CF"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;">评论总数</span></td>
        </tr>
        <tr bgcolor="#279FE0">
          <td height="92"><div align="right" style="font-size:42px;color:white;padding-right:10px;"><?php echo $totalRows_comment;?></div></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#3DA9E3"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;"><a href="/admin/user_comments/index.php" style="color:#FFFFFF;">查看更多</a>...</span></td>
        </tr>
      </table>
    </div></td>
    <td><div align="right">
      <table width="385" height="148" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td height="28" bgcolor="#1E91CF"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;">咨询总数</span></td>
        </tr>
        <tr bgcolor="#279FE0">
          <td height="92"><div align="right" style="font-size:42px;color:white;padding-right:10px;"><?php echo $totalRows_product_consult;?></div></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#3DA9E3"><span class="STYLE2" style="font-size:12px;color:white;padding-left:8px;"><a href="/admin/user_consult/index.php" style="color:#FFFFFF;">查看更多</a>...</span></td>
        </tr>
      </table>
    </div>
    </td>
  </tr>
</table>
<br />
<?php if ($totalRows_recent_orders > 0) { // Show if recordset not empty ?>
  <table   width="100%" border="1" style="border-collapse:collapse;border-top:2px solid #bfbfbf;" cellpadding="0" cellspacing="0" bordercolor="#e8e8e8">
    <tr >
      <td height="44" style="padding-left:20px;" >最新订单</td>
    </tr>
    <tr style="border-top-width:0px;">
      <td height="33"><table style="padding-left:20px;" width="100%" border="0">
        <tr>
          <td width="23%">订单ID</td>
            <td width="16%">会员</td>
            <td width="30%">创建日期</td>
            <td width="17%">总计</td>
            <td width="14%">操作</td>
          </tr>
      </table></td>
    </tr>
    <?php do { ?>
      <tr style="border-top-width:0px;">
        <td height="52"><table  style="padding-left:20px;"  height="52" width="100%" border="0">
          <tr>
            <td width="23%"><?php echo $row_recent_orders['sn']; ?></td>
              <td width="16%"><?php echo $row_recent_orders['username']; ?></td>
              <td width="30%"><?php echo $row_recent_orders['create_time']; ?></td>
              <td width="17%"><?php echo $row_recent_orders['should_paid']; ?></td>
              <td width="14%"><a href="/admin/order/detail.php?recordID=<?php echo $row_recent_orders['id']; ?>">查看</a></td>
            </tr>
        </table></td>
      </tr>
      <?php } while ($row_recent_orders = mysql_fetch_assoc($recent_orders)); ?>
      </table>
  <?php } // Show if recordset not empty ?><br />
<table  width="100%" border="1" style="border-collapse:collapse;border-top:2px solid #bfbfbf;" cellpadding="0" cellspacing="0" bordercolor="#e8e8e8">
  <tr >
    <td height="44" style="padding-left:20px;">订单统计信息</td>
  </tr>
  <tr style="border-top-width:0px;">
    <td height="52"><table style="padding-left:20px;" width="100%" border="0">
      <tr>
        <td width="25%" height="33"><a href="/admin/order/index.php?status=100">未发货订单:</a></td>
        <td width="25%" height="33"><?php echo $totalRows_paid ?> </td>
        <td width="25%" height="33"><a href="/admin/order/index.php?status=-100">已撤销订单:</a></td>
        <td height="33"><?php echo $totalRows_withdrawled ?></td>
      </tr>
      <tr>
        <td height="33"><a href="/admin/order/index.php?status=0">未支付订单:</a></td>
        <td height="33"><?php echo $totalRows_unpaied ?></td>
        <td height="33"><a href="/admin/order/index.php?status=300">已完成订单:</a></td>
        <td height="33"><?php echo $totalRows_finished ?> </td>
      </tr>
      <tr>
        <td height="33"><a href="/admin/order/index.php?status=-200">已退货订单:</a></td>
        <td height="33"><?php echo $totalRows_returned ?> </td>
        <td height="33"><a href="/admin/order/index.php?status=-300">已退款订单:</a></td>
        <td height="33"><?php echo $totalRows_refunded ?> </td>
      </tr>
    </table></td>
  </tr>
</table>
<br />
<table width="100%" border="1" style="border-collapse:collapse;border-top:2px solid #bfbfbf;" cellpadding="0" cellspacing="0" bordercolor="#e8e8e8">
  <tr >
    <td height="44" style="padding-left:20px;">系统信息</td>
  </tr>
  <tr style="border-top-width:0px;">
    <td height="52"><table style="padding-left:20px;" width="100%" border="0">
      <tr>
        <td width="25%" height="33">服务器操作系统:</td>
        <td width="25%" height="33"><?php echo PHP_OS;?></td>
        <td width="25%" height="33">网站服务器版本:</td>
        <td height="33">&nbsp;</td>
      </tr>
      <tr>
        <td height="33">PHP版本:</td>
        <td height="33"><?php echo phpversion();?></td>
        <td height="33">123PHPSHOP版本:</td>
        <td height="33">1.2</td>
      </tr>
      <tr>
        <td height="33">网站目录:</td>
        <td height="33"><?php echo $_SERVER['DOCUMENT_ROOT'];?></td>
        <td height="33">&nbsp;</td>
        <td height="33">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
<p align="left">&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($orders);

mysql_free_result($users);

mysql_free_result($unpaied);

mysql_free_result($finished);

mysql_free_result($refunded);

mysql_free_result($withdrawled);

mysql_free_result($paid);

mysql_free_result($returned);

mysql_free_result($recent_orders);
?>
