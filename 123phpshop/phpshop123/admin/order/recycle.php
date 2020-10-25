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
mysql_select_db($database_localhost, $localhost);
$query_orders = "SELECT * FROM orders WHERE is_delete = 1";
$orders = mysql_query($query_orders, $localhost) or die(mysql_error());
$row_orders = mysql_fetch_assoc($orders);
$totalRows_orders = mysql_num_rows($orders);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">订单回收站</p>

  <?php if ($totalRows_orders > 0) { // Show if recordset not empty ?>
    <table width="100%" border="1" class="phpshop123_list_box">
      
      <tr>
        <th width="12%" scope="col">订单序列号</th>
        <th scope="col"><div align="right">操作</div></th>
      </tr>
      <?php do { ?>
        <tr>
          <th scope="col"><a href="detail.php?recordID=<?php echo $row_orders['id']; ?>"><?php echo $row_orders['sn']; ?></a></th>
          <th scope="col"><div align="right"><a onClick="return confirm('您确认要恢复这个订单么？')"href="unrecycle.php?id=<?php echo $row_orders['id']; ?>">恢复</a></div></th>
        </tr>
        <?php } while ($row_orders = mysql_fetch_assoc($orders)); ?>
  </table>
    <?php } // Show if recordset not empty ?>

<?php if ($totalRows_orders == 0) { // Show if recordset empty ?>
    <span class="phpshop123_infobox">回收站里面空空如也！</span>
  <?php } // Show if recordset empty ?></body>
</html>
<?php
mysql_free_result($orders);
?>
