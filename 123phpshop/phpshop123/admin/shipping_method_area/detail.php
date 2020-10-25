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
<?php require_once('../../Connections/localhost.php'); 
$colname_DetailRS1 = "-1";
if (isset($_GET['shipping_method_id'])) {
  $colname_DetailRS1 = (get_magic_quotes_gpc()) ? $_GET['shipping_method_id'] : addslashes($_GET['shipping_method_id']);
}
mysql_select_db($database_localhost, $localhost);
$recordID = $_GET['recordID'];
$query_DetailRS1 = sprintf("SELECT * FROM shipping_method_area  WHERE id = $recordID", $colname_shipping_method);
$DetailRS1 = mysql_query($query_DetailRS1, $localhost) or die(mysql_error());
$row_DetailRS1 = mysql_fetch_assoc($DetailRS1);
$totalRows_DetailRS1 = mysql_num_rows($DetailRS1);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">运送区域详细</p>
<table border="0" align="center" class="phpshop123_form_box">
  <tr>
    <td width="13%">名称</td>
    <td width="87%"><?php echo $row_DetailRS1['name']; ?> </td>
  </tr>
  <tr>
    <td>配送方式ID</td>
    <td><?php echo $row_DetailRS1['shipping_method_id']; ?> </td>
  </tr>
  <tr>
    <td>配送区域</td>
    <td><?php echo $row_DetailRS1['area']; ?> </td>
  </tr>
  <tr>
    <td>运费计算方式</td>
    <td><?php echo $row_DetailRS1['shipping_by_quantity']; ?> </td>
  </tr>
  <tr>
    <td>基本费用</td>
    <td><?php echo $row_DetailRS1['basic_fee']; ?> </td>
  </tr>
  <tr>
    <td>首公斤费用</td>
    <td><?php echo $row_DetailRS1['first_kg_fee']; ?> </td>
  </tr>
  <tr>
    <td>续公斤费用</td>
    <td><?php echo $row_DetailRS1['continue_kg_fee']; ?> </td>
  </tr>
  <tr>
    <td>免费额度</td>
    <td><?php echo $row_DetailRS1['free_quota']; ?> </td>
  </tr>
  <tr>
    <td>货到付款费用</td>
    <td><?php echo $row_DetailRS1['cod_fee']; ?> </td>
  </tr>
  <tr>
    <td>单品费用</td>
    <td><?php echo $row_DetailRS1['single_product_fee']; ?> </td>
  </tr>
  <tr>
    <td>首500克费用</td>
    <td><?php echo $row_DetailRS1['half_kg_fee']; ?> </td>
  </tr>
  <tr>
    <td>续500克费用</td>
    <td><?php echo $row_DetailRS1['continue_half_kg_fee']; ?> </td>
  </tr>
  <tr>
    <td>首重费用</td>
    <td><?php echo $row_DetailRS1['first_weight_fee']; ?> </td>
  </tr>
</table>
</body>
</html><?php
mysql_free_result($DetailRS1);
?>
