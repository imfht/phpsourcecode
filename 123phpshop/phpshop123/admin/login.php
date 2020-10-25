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
<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/localhost.php'); ?>
<?php

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {


	
  $loginUsername=$_POST['username'];
  $password=md5($_POST['password']);
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php?error=1";
  $MM_redirecttoReferrer = true;
  
   //	  检查是否输入了验证码？如果么有输入,或是输入的验证码是否和SESSION中的验证码不一致，那么直接跳转到失败页面
  if(!isset($_POST['captcha']) OR $_POST['captcha']!=$_SESSION['captcha']){
  		 header("Location: ". $MM_redirectLoginFailed );
		 return;
  }
  
  mysql_select_db($database_localhost, $localhost);
   $LoginRS__query=sprintf("SELECT id,username,password FROM member WHERE username='%s' AND password='%s' and is_delete=0",
    get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), get_magic_quotes_gpc() ? $password : addslashes($password)); 
   
  $LoginRS = mysql_query($LoginRS__query, $localhost) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	
	//	获取这个纪录
	$user_rs=mysql_fetch_assoc( $LoginRS);
		
	
    //declare two session variables and assign them
    $_SESSION['admin_username'] = $loginUsername;
  	$_SESSION['admin_id'] = $user_rs['id'];	
	$last_login_at=date('Y-m-d H:i:s');
	$last_login_ip=$_SERVER['REMOTE_ADDR'];
	
	$update_last_login_sql="update member set last_login_at='".$last_login_at."', last_login_ip='".$last_login_ip."' where id=".$user_rs['id'];
	mysql_query($update_last_login_sql, $localhost);
	
	
    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
table{
	border-collapse:collapse;
}
table{
	font-size:14px;
}
.STYLE1 {
	font-size: 22px;
	font-weight: bold;
}
</style>
</head>

<body style="margin:0px;">

<table width="100%"  height="46" border="1" cellpadding="0" cellspacing="0" bordercolor="#E5E5E5" bgcolor="#FFFFFF">
  <tr >
    <td><span class="STYLE1">123PHPSHOP</span></td>
  </tr>
</table>
<form id="login_form" name="login_form" method="POST" action="<?php echo $loginFormAction; ?>">
  <p>&nbsp;</p>
  <table  style="border-top:3px solid #bfbfbf;" width="600" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#e8e8e8">
    <tr>
      <td height="41" bordercolor="#e8e8e8" bgcolor="#fcfcfc">&nbsp;&nbsp;&nbsp;123PHPSHOP登陆</td>
    </tr>
    <tr>
      <td valign="top">
	  <table width="100%" border="0">
        <tr>
          <td width="14%" height="77"><div align="right"><strong>用户名:</strong></div></td>
          <td width="86%"><label>
            <input name="username" type="text" id="username" style="padding-left:10px;border-radius:3px;padding-left:10px;margin-left:10px;width:90%;height:33px;border:1px solid #cccccc;" maxlength="16" />
          </label>           </td>
        </tr>
        <tr>
          <td><div align="right"><strong>密码:</strong></div></td>
          <td height="77"><label>
            <input name="password" type="password" id="password" style="padding-left:10px;border-radius:3px;padding-left:10px;margin-left:10px;width:90%;height:33px;border:1px solid #cccccc;" maxlength="16" />
          </label>           </td>
        </tr>
        <tr>
          <td align="right" valign="bottom"><strong>验证码:</strong></td>
          <td height="" align="right" valign="bottom"><div align="left">
            <label>
            <input name="captcha" type="text" size="4" maxlength="4" style="padding-left:10px;border-radius:3px;padding-left:10px;margin-left:10px;height:33px;border:1px solid #cccccc;"/>
            </label>
            <img height="37" style="cursor:pointer;" title="点击刷新" src="/captcha.php" align="absbottom" onclick="this.src='/captcha.php?'+Math.random();"></div>
            </img></td>
        </tr>
        <tr>
          <td align="right" valign="bottom">&nbsp;</td>
          <td height="76" align="right" valign="bottom"><input style="margin:15px;width:70px;height:35px;background-color:#1e91cf;border:#1978ab;color:#FFFFFF;"  type="submit" name="Submit" value="登录" />&nbsp;</td>
        </tr>
      </table></td>
    </tr>
  </table>
  <label><br />
  <br />
  </label>
  <label><br />
  <br />
  <br />
  </label>
</form>
<div align="center" style="position:absolute;bottom:120px;text-align:center;width:100%">上海序程信息科技有限公司© 2015 版权所有。
</div>
</body>
</html>
