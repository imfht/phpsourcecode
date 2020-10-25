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
  $insertSQL = sprintf("INSERT INTO shipping_method (name, `desc`, config_file_path, is_activated) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['desc'], "text"),
                       GetSQLValueString($_POST['config_file_path'], "text"),
                       GetSQLValueString(isset($_POST['is_activated']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($insertSQL, $localhost) or die(mysql_error());

  $insertGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>">
  <p class="phpshop123_title">添加配送方式</p>
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">名称:</td>
      <td><input name="name" type="text" value="" size="32" maxlength="32">
*</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">激活:</td>
      <td><input name="is_activated" type="checkbox" value="" checked="checked" ></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">配置文件夹:</td>
      <td><input name="config_file_path" type="text" id="config_file_path" value="" size="32" maxlength="100" />
*</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right" valign="top">介绍:</td>
      <td><textarea name="desc" cols="50" rows="5"></textarea>      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="添加"></td>
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
            config_file_path: {
				 required: true,
 				maxlength:100
             },
            desc: {
                maxlength:100
            }
        },
        messages: {
            name: {
                required: "必填" 
            },
            config_file_path: {
				 required: "必填",
                maxlength:"最多100个字符哦"
              },
            desc: {
                maxlength:"最多100个字符哦"
            } 
        }
    });
	
});</script>
</body>
</html>
