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
$colname_areas = "0";
if (isset($_GET['pid'])) {
  $colname_areas = (get_magic_quotes_gpc()) ? $_GET['pid'] : addslashes($_GET['pid']);
}
mysql_select_db($database_localhost, $localhost);
$query_areas = sprintf("SELECT * FROM area WHERE pid = %s", $colname_areas);
$areas = mysql_query($query_areas, $localhost) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);
$totalRows_areas = mysql_num_rows($areas);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">地址列表</p>
<table width="100%" border="0" align="center" class="phpshop123_list_box">
  <tr>
    <td><p>ID</p>    </td>
    <td>名称</td>
    <td>深度</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_areas['id']; ?>&nbsp; </td>
      <td><a href="detail.php?recordID=<?php echo $row_areas['id']; ?>"> <?php echo $row_areas['name']; ?>&nbsp; </a> </td>
      <td><?php echo $row_areas['level_depth']; ?>&nbsp; </td>
    </tr>
    <?php } while ($row_areas = mysql_fetch_assoc($areas)); ?>
</table>
<br>
<?php echo $totalRows_areas ?> 记录 总数
</p>
</body>
</html>
<?php
mysql_free_result($areas);
?>
