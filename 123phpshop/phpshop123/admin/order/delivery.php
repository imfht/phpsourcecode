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
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_order = "-1";
if (isset($_GET['id'])) {
  $colname_order = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE orders SET express_company_id=%s,express_sn=%s,order_status=%s, delivery_at=%s WHERE id=%s",
                       GetSQLValueString($_POST['express_company_id'], "int"),
					   GetSQLValueString($_POST['express_sn'], "text"),
					   GetSQLValueString(ORDER_STATUS_DELIVERED, "int"),
                       GetSQLValueString(date('Y-m-d H:i:s'), "date"),
                       GetSQLValueString($colname_order, "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
  
  
$order_log_sql="insert into order_log(order_id,message)values('".$colname_order."','".商家已发货."')";
mysql_query($order_log_sql, $localhost);
		
  $MM_redirectLoginSuccess="index.php";
  header("Location: " . $MM_redirectLoginSuccess );
  
}


mysql_select_db($database_localhost, $localhost);
$query_order = sprintf("SELECT * FROM orders WHERE id = %s", $colname_order);
$order = mysql_query($query_order, $localhost) or die(mysql_error());
$row_order = mysql_fetch_assoc($order);
$totalRows_order = mysql_num_rows($order);

mysql_select_db($database_localhost, $localhost);
$query_logistics = "SELECT * FROM express_company";
$logistics = mysql_query($query_logistics, $localhost) or die(mysql_error());
$row_logistics = mysql_fetch_assoc($logistics);
$totalRows_logistics = mysql_num_rows($logistics);

 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>发货</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">发货：<?php echo $row_order['sn']; ?></p>
<table width="100%" height="31" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th scope="row">收货人</th>
    <td><?php echo $row_order['consignee_name']; ?> <?php echo $row_order['consignee_mobile']; ?> <?php echo $row_order['consignee_province']; ?> <?php echo $row_order['consignee_city']; ?> <?php echo $row_order['consignee_district']; ?> <?php echo $row_order['consignee_address']; ?> <?php echo $row_order['consignee_zip']; ?></td>
  </tr>
</table>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table width="100%" border="0" class="phpshop123_list_box">
    <tr>
      <th scope="col">快递公司</th>
      <th scope="col">快递单号</th>
      <th scope="col">操作</th>
    </tr>
    <tr>
      <td><label>
        <div align="center">
          <select name="express_company_id" id="express_company_id">
            <?php
do {  
?>
            <option value="<?php echo $row_logistics['id']?>"<?php if (!(strcmp($row_logistics['id'], $row_order['express_company_id']))) {echo "selected=\"selected\"";} ?>><?php echo $row_logistics['name']?></option>
            <?php
} while ($row_logistics = mysql_fetch_assoc($logistics));
  $rows = mysql_num_rows($logistics);
  if($rows > 0) {
      mysql_data_seek($logistics, 0);
	  $row_logistics = mysql_fetch_assoc($logistics);
  }
?>
          </select>
        </div>
      </label></td>
      <td><div align="center">
        <input name="express_sn" type="text" value="<?php echo $row_order['express_sn']; ?>" />
      </div></td>
      <td><label>
        <div align="center">
          <input name="submit" type="submit" value="发货" />
        </div>
      </label></td>
    </tr>
  </table>
  <p>
    <input type="hidden" name="MM_update" value="form1">
    <input type="hidden" name="id" value="<?php echo $row_order['id']; ?>">
</p>
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($order);
mysql_free_result($logistics);
?>
