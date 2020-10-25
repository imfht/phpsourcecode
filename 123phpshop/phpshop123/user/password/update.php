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

  $updateSQL = sprintf("UPDATE user SET password=%s WHERE id=%s",
                       GetSQLValueString(md5($_POST['password2']), "text"),
                       GetSQLValueString($_SESSION['user_id'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());
 
}

$colname_user = "-1";
if (isset($_POST['user_id'])) {
  $colname_user = (get_magic_quotes_gpc()) ? $_POST['user_id'] : addslashes($_POST['user_id']);
}
mysql_select_db($database_localhost, $localhost);
$query_user = sprintf("SELECT id, password FROM `user` WHERE id = %s", $colname_user);
$user = mysql_query($query_user, $localhost) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);

$colname_password = "-1";
if (isset($_POST['password'])) {
  $colname_password = (get_magic_quotes_gpc()) ? $_POST['password'] : addslashes($_POST['password']);
}
mysql_select_db($database_localhost, $localhost);
$query_password = sprintf("SELECT * FROM `user` WHERE password = '%s'", $colname_password);
$password = mysql_query($query_password, $localhost) or die(mysql_error());
$row_password = mysql_fetch_assoc($password);
$totalRows_password = mysql_num_rows($password);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_user.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_user_title">更新密码 </p>
<form method="post" name="form1" id="form1"  action="<?php echo $editFormAction; ?>">
  <table align="center" class="phpshop123_user_form_box">
    <tr valign="baseline">
      <td nowrap align="right">旧密码:</td>
      <td><input  name="password" type="password"  id="password" value="<?php echo $row_user['password']; ?>" size="32" maxlength="16"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">新密码</td>
      <td><input name="password2" type="password" id="password2" value="<?php echo $row_user['password']; ?>" size="32" maxlength="16" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">新密码</td>
      <td><input name="password3" type="password" id="password3" value="<?php echo $row_user['password']; ?>" size="32" maxlength="16" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新密码"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
</form>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script>
$().ready(function(){

	$("#form1").validate({
        rules: {
            password: {
                required: true,
                minlength: 8,
				remote:{
                    url: "ajax_password.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'password': function(){return $("#password").val();}
                    }
					}
            },
            password2: {
                required: true,
                minlength: 8 
				  
            },
            password3: {
                required: true,
                minlength: 8 ,
				equalTo:"#password2"
            } 
        },
        messages: {
            password: {
                required: "必填",
                minlength: "最少要8个字符哦",
				remote:"旧密码不正确"
            },
            password2: {
                required: "必填",
                minlength: "最少要8个字符哦"
              },
            password3: {
                required: "必填",
                minlength: "最少要8个字符哦",
				equalTo:"两个密码不一致哦"
            } 
        }
    });
	
});</script>
</body>
</html>
<?php
mysql_free_result($user);

mysql_free_result($password);
?>
