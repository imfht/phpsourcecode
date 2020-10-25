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

	$colname_attr = "-1";
	if (isset($_GET['id'])) {
	  $colname_attr = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
	}
	mysql_select_db($database_localhost, $localhost);
	$query_attr = sprintf("SELECT * FROM product_type_attr WHERE id = %s", $colname_attr);
	$attr = mysql_query($query_attr, $localhost) or die(mysql_error());
	$row_attr = mysql_fetch_assoc($attr);
 
   $updateSQL = sprintf("UPDATE product_type_attr SET name=%s, is_selectable=%s, input_method=%s, selectable_value=%s, product_type_id=%s WHERE id=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['is_selectable'], "int"),
                       GetSQLValueString($_POST['input_method'], "int"),
                       GetSQLValueString($_POST['selectable_value'], "text"),
                       GetSQLValueString($_POST['product_type_id'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());

  $updateGoTo = "index.php?product_type_id=".$row_attr['product_type_id'];
   header(sprintf("Location: %s", $updateGoTo));
}

$colname_attr = "-1";
if (isset($_GET['id'])) {
  $colname_attr = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_attr = sprintf("SELECT * FROM product_type_attr WHERE id = %s", $colname_attr);
$attr = mysql_query($query_attr, $localhost) or die(mysql_error());
$row_attr = mysql_fetch_assoc($attr);
$totalRows_attr = mysql_num_rows($attr);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">属性编辑：
  <?php echo $row_attr['name']; ?></p>
<p>&nbsp;</p>

 

<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">属性:</td>
      <td><input type="text" name="name" value="<?php echo $row_attr['name']; ?>" size="32">
      *</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">是否可选:</td>
      <td valign="baseline"><input type="radio" name="is_selectable" value="1" <?php if (!(strcmp($row_attr['is_selectable'],1))) {echo "CHECKED";} ?> />
只是显示
  <input type="radio" name="is_selectable" value="2" <?php if (!(strcmp($row_attr['is_selectable'],2))) {echo "CHECKED";} ?> />
可单选</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">输入方式:</td>
      <td valign="baseline"><input type="radio" name="input_method" value="1" <?php if (!(strcmp($row_attr['input_method'],1))) {echo "CHECKED";} ?> />
手动录入
<input type="radio" name="input_method" value="2" <?php if (!(strcmp($row_attr['input_method'],2))) {echo "CHECKED";} ?> />
从一下列表中选</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">可选值:</td>
      <td><textarea name="selectable_value" cols="50" rows="5"><?php echo $row_attr['selectable_value']; ?></textarea>
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新"></td>
    </tr>
  </table>
  <input type="hidden" name="product_type_id" value="<?php echo $row_attr['product_type_id']; ?>">
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id" value="<?php echo $row_attr['id']; ?>">
</form>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script>
$().ready(function(){

	$("#form1").validate({
        rules: {
            name: {
                required: true
            },
            image_width: {
                required: true,
				digits:true
				  
            },
            image_height: {
                required: true,
				digits:true
            } ,
            intro: {
                 maxlength: 1000  
            }
        },
        messages: {
            name: {
                required: "必填" 
            },
            image_width: {
                required: "必填" ,
				digits:"必须是整数哦"
              },
            image_height: {
                required: "必填",
				digits:"必须是整数哦"
            } ,
            intro: {
                 maxlength:"最多1000个字符哦"
            }
        }
    });
	
});</script>
</body>
</html>
<?php
mysql_free_result($attr);
?>
