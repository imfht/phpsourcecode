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
	
  $updateSQL = sprintf("UPDATE user SET username=%s, password=%s, email=%s, mobile=%s, gender=%s, birth_date=%s, province=%s, city=%s, district=%s, address=%s WHERE id=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString(md5($_POST['password']), "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['mobile'], "text"),
                       GetSQLValueString($_POST['gender'], "int"),
                       GetSQLValueString($_POST['birth_date'], "date"),
                       GetSQLValueString($_POST['province'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['district'], "text"),
                       GetSQLValueString($_POST['address'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

	if(empty($_POST['password']) || !isset($_POST['password'])){
		$updateSQL = sprintf("UPDATE user SET username=%s, email=%s, mobile=%s, gender=%s, birth_date=%s, province=%s, city=%s, district=%s, address=%s WHERE id=%s",
						   GetSQLValueString($_POST['username'], "text"),
 						   GetSQLValueString($_POST['email'], "text"),
						   GetSQLValueString($_POST['mobile'], "text"),
						   GetSQLValueString($_POST['gender'], "int"),
						   GetSQLValueString($_POST['birth_date'], "date"),
						   GetSQLValueString($_POST['province'], "text"),
						   GetSQLValueString($_POST['city'], "text"),
						   GetSQLValueString($_POST['district'], "text"),
						   GetSQLValueString($_POST['address'], "text"),
						   GetSQLValueString($_POST['id'], "int"));
	}
	
  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($updateSQL, $localhost) or die(mysql_error());

  $updateGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_user = "-1";
if (isset($_GET['id'])) {
  $colname_user = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_user = sprintf("SELECT * FROM `user` WHERE id = %s", $colname_user);
$user = mysql_query($query_user, $localhost) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<p class="phpshop123_title">
 
更新用户信息</p>
<p>&nbsp; </p>

<form action="<?php echo $editFormAction; ?>" method="post" name="update_user_form" id="update_user_form">
  <table align="center" class="phpshop123_form_box">
    <tr valign="baseline">
      <td nowrap align="right">账号:</td>
      <td><input type="text" name="username" id="username"  value="<?php echo $row_user['username']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">密码:</td>
      <td><input type="password" name="password"  id="password"  value="" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">密码确认:</td>
      <td><input name="password2" type="password"  id="password2" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">邮箱:</td>
      <td><input type="text" name="email"  id="email"  value="<?php echo $row_user['email']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">手机:</td>
      <td><input name="mobile" type="text"  id="mobile"  value="<?php echo $row_user['mobile']; ?>" size="32" maxlength="11"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">性别:</td>
      <td valign="baseline"><input type="radio" name="gender" value="1" <?php if (!(strcmp($row_user['gender'],1))) {echo "CHECKED";} ?> />
男
<input type="radio" name="gender" value="0" <?php if (!(strcmp($row_user['gender'],0))) {echo "CHECKED";} ?> />
女</td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">生日:</td>
      <td><input type="text"  id="birth_date"  name="birth_date" value="<?php echo $row_user['birth_date']; ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">省市:</td>
      <td>
<select name="province"  id="province"  >
</select>
<select name="city"  id="city"  >
</select>
<select name="district"  id="district"  >
</select>
		 </td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">地址:</td>
      <td><input type="text"  id="address"   name="address" value="<?php echo $row_user['address'];?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="更新记录"></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id"  id="id"   value="<?php echo $row_user['id']; ?>">
</form>
<link rel="stylesheet" href="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.css">
<script language="JavaScript" type="text/javascript" src="../../js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery.validate.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../../js/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/widget/area/jsAddress.js"></script>

 <script>
  
	$().ready(function(){
	addressInit('province', 'city', 'district', '<?php echo $row_user['province']; ?>', '<?php echo $row_user['city']; ?>', '<?php echo $row_user['district']; ?>');
$( "#birth_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$("#update_user_form").validate({
        rules: {
            username: {
                required: true,
                minlength: 6,
				remote:{
                    url: "ajax_update_username.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'username': function(){return $("#username").val();},
						 'id': function(){return $("#id").val();}
                    }
					}
            },
            password: {
                 minlength: 8   
            },
            password2: {
                minlength: 8 ,
				equalTo:"#password"
            },
			mobile: {
				
                required: true,
                minlength: 11,
				remote:{
                    url: "ajax_update_mobile.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'mobile': function(){return $("#mobile").val();},
						 'id': function(){return $("#id").val();}
                    }
					}
            },
			email: {
				email:true,
                required: true,
                minlength: 6,
				remote:{
                    url: "ajax_update_email.php",
                    type: "post",
                    dataType: 'json',
                    data: {
                        'email': function(){return $("#email").val();},
						 'id': function(){return $("#id").val();}
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
	
});

 </script>


</body>
</html>
<?php
mysql_free_result($user);
?>
