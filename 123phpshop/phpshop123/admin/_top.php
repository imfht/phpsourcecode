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
  $_SESSION['admin_username'] = NULL;
  $_SESSION['admin_id'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['admin_username']);
  unset($_SESSION['admin_id']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
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
#admin_top {
	background-color: #FFFFFF;
	margin-top: 0px;
	margin-left: 0px;
	border-bottom:1px solid #E5E5E5;
	height:45px;
	width:100%;
	
}

a{
	text-decoration:none;
	color:black;
}

#admin_top_left{
	float:left;
	height:inherit;
	line-height:45px;
	font-size:44px;
}
#admin_logout{
	float:right;
	height:inherit;
	line-height:45px;
}

body {
	margin-left: 0px;
	margin-top: 0px;
}

-->
</style></head>

<body style="font-family:微软雅黑;">
 <div id="admin_top">
	 <div id='admin_top_left'>123PHPSHOP</div>
	 <div id="admin_logout"><a href="<?php echo $logoutAction ?>" target="_parent">安全退出</a></div>
 </div>

</body>
</html>
