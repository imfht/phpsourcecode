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
	$insertSQL = sprintf("INSERT INTO product_consult (content, to_question,user_id) VALUES (%s, %s, %s)",
					   GetSQLValueString($_POST['content'], "text"),
					   GetSQLValueString($_POST['to_question'], "int"),
					   GetSQLValueString($_SESSION['admin_id'], "int"));
	
	mysql_select_db($database_localhost, $localhost);
	$Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

	$update_sql=sprintf("update product_consult set is_replied=1 where id=%s",GetSQLValueString($_POST['to_question'], "int"));
	$Result1 = mysql_query($update_sql, $localhost) or die(mysql_error());
	
  $insertGoTo = "index.php";
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_consult = "-1";
if (isset($_GET['id'])) {
  $colname_consult = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_consult = sprintf("SELECT * FROM product_consult WHERE id = %s", $colname_consult);
$consult = mysql_query($query_consult, $localhost) or die(mysql_error());
$row_consult = mysql_fetch_assoc($consult);
$totalRows_consult = mysql_num_rows($consult);

$colname_replies = "-1";
if (isset($_GET['id'])) {
  $colname_replies = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_replies = sprintf("SELECT * FROM product_consult WHERE to_question = %s", $colname_replies);
$replies = mysql_query($query_replies, $localhost) or die(mysql_error());
$row_replies = mysql_fetch_assoc($replies);
$totalRows_replies = mysql_num_rows($replies);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php if ($totalRows_replies > 0) { // Show if recordset not empty ?>
  <p class="phpshop123_title">历史回复</p>
  <table width="100%" border="1" class="phpshop123_list_box">
    <tr>
      <th scope="col">内容</th>
      <th scope="col">创建时间</th>
      <th scope="col">回复人</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_replies['content']; ?></td>
        <td><?php echo $row_replies['create_time']; ?></td>
        <td><?php echo $row_replies['user_id']; ?></td>
      </tr>
      <?php } while ($row_replies = mysql_fetch_assoc($replies)); ?>
      </table>
  <?php } // Show if recordset not empty ?><p class="phpshop123_title">咨询回答</p>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">问题</td>
      <td><?php echo $row_consult['content']; ?></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">回答:</td>
      <td><textarea name="content" cols="80" rows="10">你好，</textarea></td>
    </tr>
      <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="回答"></td>
    </tr>
  </table>
  <input type="hidden" name="to_question" value="<?php echo $row_consult['id']; ?>" />
  <input type="hidden" name="MM_insert" value="form1">
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($consult);

mysql_free_result($replies);
?>
