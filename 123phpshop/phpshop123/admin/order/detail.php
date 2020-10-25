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
<?php require_once('../../Connections/localhost.php'); ?><?php
$colname_products = "-1";
if (isset($_GET['recordID'])) {
  $colname_products = (get_magic_quotes_gpc()) ? $_GET['recordID'] : addslashes($_GET['recordID']);
}
mysql_select_db($database_localhost, $localhost);
$query_products = sprintf("SELECT order_item.*,product.name as product_name FROM order_item inner join product on product.id=order_item.product_id WHERE order_item.order_id = %s", $colname_products);
$products = mysql_query($query_products, $localhost) or die(mysql_error());
$row_products = mysql_fetch_assoc($products);
$totalRows_products = mysql_num_rows($products);
  
$maxRows_DetailRS1 = 50;
$pageNum_DetailRS1 = 0;
if (isset($_GET['pageNum_DetailRS1'])) {
  $pageNum_DetailRS1 = $_GET['pageNum_DetailRS1'];
}
$startRow_DetailRS1 = $pageNum_DetailRS1 * $maxRows_DetailRS1;

mysql_select_db($database_localhost, $localhost);
$recordID = $_GET['recordID'];
$query_DetailRS1 = "SELECT orders.*,shipping_method.name as shipping_method_name,user.username FROM `orders` inner join user on user.id=orders.user_id left join shipping_method on orders.shipping_method=shipping_method.id WHERE orders.id = $recordID ";
$query_limit_DetailRS1 = sprintf("%s LIMIT %d, %d", $query_DetailRS1, $startRow_DetailRS1, $maxRows_DetailRS1);
$DetailRS1 = mysql_query($query_limit_DetailRS1, $localhost) or die(mysql_error());
$row_DetailRS1 = mysql_fetch_assoc($DetailRS1);

if (isset($_GET['totalRows_DetailRS1'])) {
  $totalRows_DetailRS1 = $_GET['totalRows_DetailRS1'];
} else {
  $all_DetailRS1 = mysql_query($query_DetailRS1);
  $totalRows_DetailRS1 = mysql_num_rows($all_DetailRS1);
}
$totalPages_DetailRS1 = ceil($totalRows_DetailRS1/$maxRows_DetailRS1)-1;


mysql_select_db($database_localhost, $localhost);
$query_log_DetailRS1 = "SELECT * FROM `order_log`  WHERE order_id = $recordID";
$log_DetailRS1 = mysql_query($query_log_DetailRS1, $localhost);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
		
<p class="phpshop123_title">订单详细</p>
<table width="100%" border="0" align="center" class="phpshop123_form_box">
  
  <tr>
    <td>ID</td>
    <td><?php echo $row_DetailRS1['id']; ?> </td>
  </tr>
  <tr>
    <td>序列号</td>
    <td><?php echo $row_DetailRS1['sn']; ?> </td>
  </tr>
  <tr>
    <td>用户</td>
    <td><?php echo $row_DetailRS1['username']; ?> </td>
  </tr>
  <tr>
    <td>应付</td>
    <td>￥<?php echo $row_DetailRS1['should_paid']; ?> </td>
  </tr>
  <tr>
    <td>商品总额</td>
    <td>￥<?php echo $row_DetailRS1['products_total']; ?> </td>
  </tr>
  <tr>
    <td>运费</td>
    <td>￥<?php echo $row_DetailRS1['shipping_fee']; ?> </td>
  </tr>
  <tr>
    <td>订单状态</td>
    <td><?php echo $order_status[$row_DetailRS1['order_status']];; ?> </td>
  </tr>
   <tr>
    <td>创建时间</td>
    <td><?php echo $row_DetailRS1['create_time']; ?> </td>
  </tr>
  <tr>
    <td>快递方式</td>
    <td><?php echo $row_DetailRS1['shipping_method_name']; ?> </td>
  </tr>
  <tr>
    <td>支付方式</td>
    <td><?php echo $pay_methomd[$row_DetailRS1['payment_method']]; ?> </td>
  </tr>
  <tr>
    <td>需要发票</td>
    <td><?php echo $row_DetailRS1['invoice_is_needed']=='0'?"否":"√"; ?> </td>
  </tr>
  <?php if($row_DetailRS1['invoice_is_needed']=='1'){ ?>
  <tr>
    <td>发票抬头</td>
    <td><?php echo $row_DetailRS1['invoice_title']; ?> </td>
  </tr>
  <tr>
    <td>发票留言</td>
    <td><?php echo $row_DetailRS1['invoice_message']; ?> </td>
  </tr>
  <?php } ?>
  <tr>
    <td>可收货时间</td>
    <td><?php echo $row_DetailRS1['please_delivery_at']==null?"未设置":$please_deliver_at[$row_DetailRS1['please_delivery_at']]; ?> </td>
  </tr>
  <tr>
    <td>备注</td>
    <td><?php echo $row_DetailRS1['memo']==null?"未设置":$row_DetailRS1['memo']; ?> </td>
  </tr>
</table>

<p>货品列表</p>
<table width="100%" border="1" class="phpshop123_list_box">
  <tr>
    <th scope="col">名称</th>
    <th scope="col">数量</th>
    <th scope="col">实付</th>
    <th scope="col">应付</th>
  </tr>
  <?php do { ?>
    <tr>
      <td scope="col"><?php echo $row_products['product_name']; ?> <span style="color:#999999"><?php echo str_replace(";","	",$row_products['attr_value']); ?></span></td>
      <td scope="col"><?php echo $row_products['quantity']; ?></td>
      <td scope="col">￥<?php echo $row_products['actual_pay_price']; ?></td>
      <td scope="col">￥<?php echo $row_products['should_pay_price']; ?></td>
    </tr>
    <?php } while ($row_products = mysql_fetch_assoc($products)); ?>
</table>
<p>订单处理过程</p>
<table width="100%" border="1"  class="phpshop123_list_box">
 <?php while ($row_log_DetailRS1 = mysql_fetch_assoc($log_DetailRS1)){ ?>
  <tr  >
    <td width="10%"><?php echo $row_log_DetailRS1['create_time'];?></td>
    <td width="90%"><?php echo $row_log_DetailRS1['message'];?></td>
  </tr>
  <?php  } ?>
</table>
 <p>收货人</p>
<table width="100%" border="1" class="phpshop123_list_box">
  <tr>
    <th scope="col">收货人姓名</th>
    <th scope="col">手机</th>
    <th scope="col">省份</th>
    <th scope="col">城市</th>
    <th scope="col">区域</th>
    <th scope="col">地址</th>
    <th scope="col">邮编</th>
  </tr>
  <tr>
    <td scope="col"><?php echo $row_DetailRS1['consignee_name']; ?></td>
    <td scope="col"><?php echo $row_DetailRS1['consignee_mobile']; ?></td>
    <td scope="col"><?php echo $row_DetailRS1['consignee_province']; ?></td>
    <td scope="col"><?php echo $row_DetailRS1['consignee_city']; ?></td>
    <td scope="col"><?php echo $row_DetailRS1['consignee_district']; ?></td>
    <td scope="col"><?php echo $row_DetailRS1['consignee_address']; ?></td>
    <td scope="col"><?php echo $row_DetailRS1['consignee_zip']; ?></td>
  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>
