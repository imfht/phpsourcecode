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
<?php require_once('../../../Connections/localhost.php'); ?>
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

	mysql_select_db($database_localhost, $localhost);
	$query_alipay_config = "SELECT * FROM pay_alipay WHERE id = 1";
	$alipay_config = mysql_query($query_alipay_config, $localhost) or die(mysql_error());
	$row_alipay_config = mysql_fetch_assoc($alipay_config);
	$totalRows_alipay_config = mysql_num_rows($alipay_config);

	if($totalRows_alipay_config==0){
		
	  $updateSQL = sprintf("insert into pay_alipay (account,security_code,cooperate_user_info)values( %s, %s, %s)",
						   GetSQLValueString($_POST['account'], "text"),
						   GetSQLValueString($_POST['security_code'], "text"),
						   GetSQLValueString($_POST['cooperate_user_info'], "text") 
	);
	  mysql_select_db($database_localhost, $localhost);
	  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
 	}else{
	
		$updateSQL = sprintf("UPDATE pay_alipay SET account=%s, security_code=%s, cooperate_user_info=%s WHERE id=%s",
						   GetSQLValueString($_POST['account'], "text"),
						   GetSQLValueString($_POST['security_code'], "text"),
						   GetSQLValueString($_POST['cooperate_user_info'], "text"),
						   GetSQLValueString($_POST['id'], "int"));
	
	  mysql_select_db($database_localhost, $localhost);
	  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
	
	}


	  
}

mysql_select_db($database_localhost, $localhost);
$query_alipay_config = "SELECT * FROM pay_alipay WHERE id = 1";
$alipay_config = mysql_query($query_alipay_config, $localhost) or die(mysql_error());
$row_alipay_config = mysql_fetch_assoc($alipay_config);
$totalRows_alipay_config = mysql_num_rows($alipay_config);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <p class="phpshop123_title">支付宝配置</p>
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">支付宝帐户:</td>
      <td><input type="text" name="account" value="<?php echo $row_alipay_config['account']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">交易安全校验码:</td>
      <td><input type="text" name="security_code" value="<?php echo $row_alipay_config['security_code']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">合作者身份ID:</td>
      <td><input type="text" name="cooperate_user_info" value="<?php echo $row_alipay_config['cooperate_user_info']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新记录"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id" value="1">
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($alipay_config);
?>
