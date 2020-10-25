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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add_user_form")) {
  $insertSQL = sprintf("INSERT INTO user (username, password, email, mobile, gender, birth_date, province, city, district, address) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s,   %s)",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString(md5($_POST['password']), "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['mobile'], "text"),
                       GetSQLValueString($_POST['gender'], "int"),
                       GetSQLValueString($_POST['birth_date'], "date"),
                       GetSQLValueString($_POST['province'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['district'], "text"),
                       GetSQLValueString($_POST['address'], "text"));
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
<p class="phpshop123_title">
  
  添加用户</p>
<form action="<?php echo $editFormAction; ?>" method="post" name="add_user_form" id="add_user_form">
  <p>&nbsp;</p>
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">账号:</td>
      <td><input type="text" name="username"  id="username" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">密码:</td>
      <td><input type="password" name="password"  id="password"  value="" size="32"></td>
    </tr>
	 <tr valign="baseline">
      <td nowrap align="right">密码确认:</td>
      <td><input type="password" name="password2"  id="password2"  value="" size="32"></td>
    </tr>
	
    <tr valign="baseline">
      <td nowrap align="right">邮箱:</td>
      <td><input type="text" name="email"   id="email" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">手机:</td>
      <td><input type="text" name="mobile"   id="mobile" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">性别:</td>
      <td valign="baseline"><input name="gender" type="radio" value="1" checked="checked" />
男
<input type="radio" name="gender" value="0" />
      女</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">生日:</td>
      <td><input type="text" name="birth_date"   id="birth_date" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">省市:</td>
      <td><?php include($_SERVER['DOCUMENT_ROOT'].'/widget/area/index.php');?></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">地址:</td>
      <td><input type="text" name="address"  id="address" value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="插入记录"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="add_user_form">
</form>
<link rel="stylesheet" href="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>

<script>
$().ready(function(){
	$( "#birth_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
	
	$("#add_user_form").validate({
        rules: {
            username: {
                required: true,
                minlength: 6,
				remote:{
                    url: "ajax_username.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'username': function(){return $("#username").val();}
                    }
					}
            },
            password: {
                required: true,
                minlength: 8   
            },
            password2: {
                required: true,
                minlength: 8 ,
				equalTo:"#password"
            },
			mobile: {
                 required: true,
                minlength: 11,
				remote:{
                    url: "ajax_mobile.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'mobile': function(){return $("#mobile").val();}
                    }
					}
            },
			email: {
				email:true,
                required: true,
                minlength: 6,
				remote:{
                    url: "ajax_email.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'email': function(){return $("#email").val();}
                    }
					}
            }
			
        },
        messages: {
			username: {
  				remote:"用户名已存在"
            },
 			email: {
  				remote:"邮件地址已经存在"
            },
			mobile: {
                
 				remote:"手机号码已经存在"
            }
        }
    });
	
});</script>
</body>
</html>
