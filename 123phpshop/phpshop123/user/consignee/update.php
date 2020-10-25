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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
 	
	// 这里需要检查这个收货人是否属于这个用户,如果不是的话
 	$colname_consignee = "-1";
	if (isset($_POST['id'])) {
	  $colname_consignee = (get_magic_quotes_gpc()) ? $_POST['id'] : addslashes($_POST['id']);
	}
	
	mysql_select_db($database_localhost, $localhost);
	$query_consignee = sprintf("SELECT * FROM user_consignee WHERE id = %s and user_id= %s", $colname_consignee,$_SESSION['user_id']);
	$consignee = mysql_query($query_consignee, $localhost) or die(mysql_error());
 	$totalRows_consignee = mysql_num_rows($consignee);
	if($totalRows_consignee==1){
  	
     $updateSQL = sprintf("UPDATE user_consignee SET name=%s, mobile=%s, province=%s, city=%s, district=%s, address=%s, zip=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['mobile'], "text"),
                       GetSQLValueString($_POST['province'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['district'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['id'], "int"));
 
	  mysql_select_db($database_localhost, $localhost);
	  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
 	  $updateGoTo = "index.php";
	  header(sprintf("Location: %s", $updateGoTo));
	}
}
$colname_consignee = "-1";
if (isset($_GET['id'])) {
  $colname_consignee = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_consignee = sprintf("SELECT * FROM user_consignee WHERE id = %s and user_id= %s ", $colname_consignee,$_SESSION['user_id']);
$consignee = mysql_query($query_consignee, $localhost) or die(mysql_error());
$row_consignee = mysql_fetch_assoc($consignee);
$totalRows_consignee = mysql_num_rows($consignee);
if($totalRows_consignee==0){
		$remove_succeed_url="index.php";
		header("Location: " . $remove_succeed_url );
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
<!--
body {
	background-color: #f5f5f5;
}
-->
</style>
<link href="/css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">更新收货地址</p>
 

<form method="post" name="form1" id="update_consignee_form" action="<?php echo $editFormAction; ?>">
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">姓名:</td>
      <td><input type="text" name="name" value="<?php echo $row_consignee['name']; ?>" size="32">*为保证收货顺利，请使用真实姓名</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">手机:</td>
      <td><input type="text" name="mobile" value="<?php echo $row_consignee['mobile']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">省市:</td>
      <td><select name="province"  id="province"  ></select><select name="city"  id="city"  ></select><select name="district"  id="district"  ></select>      </td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap align="right">地址:</td>
      <td><input type="text" name="address" value="<?php echo $row_consignee['address']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">邮编:</td>
      <td><input type="text" name="zip" value="<?php echo $row_consignee['zip']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id" value="<?php echo $row_consignee['id']; ?>">
</form>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/widget/area/jsAddress.js"></script>
<script>
$().ready(function(){
addressInit('province', 'city', 'district', '<?php echo $row_consignee['province']; ?>', '<?php echo $row_consignee['city']; ?>', '<?php echo $row_consignee['district']; ?>');
 	$("#update_consignee_form").validate({
        rules: {
		
            name: {
                required: true,
				minlength: 2,
             },
            mobile: {
                required: true,
                minlength: 11,
				digits:true   
            },
            address: {
                required: true,
                minlength: 3   
            },
 			zip: {
                required: true,
                minlength: 6,
				digits:true
            }
        } 
    });
});</script>
</body>
</html>
<?php
mysql_free_result($consignee);
?>
