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
$maxRows_products = 50;
$pageNum_products = 0;
if (isset($_GET['pageNum_products'])) {
  $pageNum_products = $_GET['pageNum_products'];
}
$startRow_products = $pageNum_products * $maxRows_products;

mysql_select_db($database_localhost, $localhost);
$query_products = "SELECT * FROM product WHERE is_delete = 1";
$query_limit_products = sprintf("%s LIMIT %d, %d", $query_products, $startRow_products, $maxRows_products);
$products = mysql_query($query_limit_products, $localhost) or die(mysql_error());
$row_products = mysql_fetch_assoc($products);

if (isset($_GET['totalRows_products'])) {
  $totalRows_products = $_GET['totalRows_products'];
} else {
  $all_products = mysql_query($query_products);
  $totalRows_products = mysql_num_rows($all_products);
}
$totalPages_products = ceil($totalRows_products/$maxRows_products)-1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">商品回收站</p>
<?php if ($totalRows_products == 0) { // Show if recordset empty ?>
    <div class="phpshop123_infobox">回收站中空空如也。</div>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_products > 0) { // Show if recordset not empty ?>
  <table width="100%" border="1" class="phpshop123_list_box">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">名称</th>
      <th scope="col">价格</th>
      <th scope="col">市场价</th>
      <th scope="col">库存</th>
      <th scope="col">创建时间</th>
      <th scope="col">操作</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_products['id']; ?></td>
        <td><?php echo $row_products['name']; ?></td>
        <td><?php echo $row_products['price']; ?></td>
        <td><?php echo $row_products['market_price']; ?></td>
        <td><?php echo $row_products['store_num']; ?></td>
        <td><?php echo $row_products['create_time']; ?></td>
        <td><div align="right"><a onClick="return confirm('您确定要恢复这个产品吗？')" href="unrecycled.php?id=<?php echo $row_products['id']; ?>">恢复</a></div></td>
      </tr>
      <?php } while ($row_products = mysql_fetch_assoc($products)); ?>
  </table>
  <?php } // Show if recordset not empty ?><p>&nbsp; </p>
</body>
</html>
<?php
mysql_free_result($products);
?>
