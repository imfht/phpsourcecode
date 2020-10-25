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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO ad_images (image_path, ad_id, link_url) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['image_path'], "text"),
                       GetSQLValueString($_POST['ad_id'], "int"),
                       GetSQLValueString($_POST['link_url'], "text"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
}

$colname_ad = "-1";
if (isset($_GET['ad_id'])) {
  $colname_ad = (get_magic_quotes_gpc()) ? $_GET['ad_id'] : addslashes($_GET['ad_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_ad = sprintf("SELECT * FROM ad WHERE id = %s", $colname_ad);
$ad = mysql_query($query_ad, $localhost) or die(mysql_error());
$row_ad = mysql_fetch_assoc($ad);
$totalRows_ad = mysql_num_rows($ad);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<p><?php echo $row_ad['name']; ?>-&gt;添加图片</p>
<p>&nbsp; </p>

<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap align="right">上传图片:</td>
      <td><input type="file" name="image_path" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">链接网址:</td>
      <td><input type="text" name="link_url" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="插入记录"></td>
    </tr>
  </table>
  <input type="hidden" name="ad_id" value="<?php echo $row_ad['id']; ?>">
  <input type="hidden" name="MM_insert" value="form1">
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($ad);
?>
