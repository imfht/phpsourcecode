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
$colname_DetailRS1 = "-1";
if (isset($_GET['pid'])) {
  $colname_DetailRS1 = (get_magic_quotes_gpc()) ? $_GET['pid'] : addslashes($_GET['pid']);
}
mysql_select_db($database_localhost, $localhost);
$recordID = $_GET['recordID'];
$query_DetailRS1 = sprintf("SELECT * FROM area  WHERE id = $recordID");
$DetailRS1 = mysql_query($query_DetailRS1, $localhost) or die(mysql_error());
$row_DetailRS1 = mysql_fetch_assoc($DetailRS1);
$totalRows_DetailRS1 = mysql_num_rows($DetailRS1);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
		
<p>区域详细:</p>
<p>&nbsp;</p>
<table border="1" align="center">
  
  <tr>
    <td>id</td>
    <td><?php echo $row_DetailRS1['id']; ?> </td>
  </tr>
  <tr>
    <td>name</td>
    <td><?php echo $row_DetailRS1['name']; ?> </td>
  </tr>
  <tr>
    <td>pid</td>
    <td><?php echo $row_DetailRS1['pid']; ?> </td>
  </tr>
  <tr>
    <td>level_depth</td>
    <td><?php echo $row_DetailRS1['level_depth']; ?> </td>
  </tr>
  <tr>
    <td>level_path</td>
    <td><?php echo $row_DetailRS1['level_path']; ?> </td>
  </tr>
  <tr>
    <td>child_num</td>
    <td><?php echo $row_DetailRS1['child_num']; ?> </td>
  </tr>
  
  
</table>

</body>
</html><?php
mysql_free_result($DetailRS1);
?>
