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
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['user_id'] = NULL;
  $_SESSION['username'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['user_id']);
  unset($_SESSION['username']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "/index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
<!--
.user_panel_logo {
	font-size: 35px;
	font-weight: bold;
	color: #FFFFFF;
}
-->
</style>
</head>

<body style="margin:0px;">
 
  <?php include_once($_SERVER['DOCUMENT_ROOT'].'/widget/top_full_nav.php'); ?>
 	
 
<table width="100%" height="80" border="0" cellpadding="0" cellspacing="0" bgcolor="#e45050">
  <tr>
    <td><table width="1210" height="80" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="265"><span class="user_panel_logo">123PHPSHOP</span></td>
        <td>&nbsp;</td>
        <td width="186">&nbsp;</td>
        <td width="141">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
