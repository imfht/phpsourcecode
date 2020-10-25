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
  $insertSQL = sprintf("INSERT INTO product_type_attr (name, is_selectable, input_method, selectable_value, product_type_id) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['is_selectable'], "int"),
                       GetSQLValueString($_POST['input_method'], "int"),
                       GetSQLValueString($_POST['selectable_value'], "text"),
                       GetSQLValueString($_POST['product_type_id'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());
  
    $insertGoTo = "index.php?product_type_id=".$_POST['product_type_id'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo)); 
  
  
}

$colname_product_type = "-1";
if (isset($_GET['product_type_id'])) {
  $colname_product_type = (get_magic_quotes_gpc()) ? $_GET['product_type_id'] : addslashes($_GET['product_type_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_product_type = sprintf("SELECT * FROM product_type WHERE id = %s", $colname_product_type);
$product_type = mysql_query($query_product_type, $localhost) or die(mysql_error());
$row_product_type = mysql_fetch_assoc($product_type);
$totalRows_product_type = mysql_num_rows($product_type);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <p class="phpshop123_title"><?php echo $row_product_type['name']; ?>：添加属性  </p>
  <table width="100%" align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">属性名称:</td>
      <td><input type="text" name="name" value="" size="32">
      *</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">是否可选:</td>
      <td valign="baseline"><input name="is_selectable" type="radio" value="1" checked="checked" />
只是显示
  <input type="radio" name="is_selectable" value="2" />
可单选</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">输入方法:</td>
      <td valign="baseline"><input name="input_method" type="radio" value="1" checked="checked" />
手动录入
  <input type="radio" name="input_method" value="2" />
从以下列表中选</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">可选值:</td>
      <td><textarea name="selectable_value" cols="50" rows="5"></textarea>
	  <input type="hidden" name="product_type_id" value="<?php echo $_GET['product_type_id']; ?>" />
	  [每个可选的值之间请用空格隔开，例如手机支持网络的可选值为：电信 联通]</td>
    </tr>
     <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="添加属性"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1">
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
mysql_free_result($product_type);
?>
