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
<?php require_once('Connections/localhost.php'); ?>
<?php 

//var_export(get_catalog_path(array(4)));die;

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){

  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['username'] = NULL;
  $_SESSION['user_id'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['username']);
  unset($_SESSION['user_id']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
mysql_select_db($database_localhost, $localhost);
$query_shopinfo = "SELECT * FROM shop_info WHERE id = 1";
$shopinfo = mysql_query($query_shopinfo, $localhost) or die(mysql_error());
$row_shopinfo = mysql_fetch_assoc($shopinfo);
$totalRows_shopinfo = mysql_num_rows($shopinfo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $row_shopinfo['name']; ?></title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin:0px;">
<?php include_once('widget/top_full_nav.php'); ?>
<?php //include_once('widget/ad/1024_one_image.php'); ?>
<?php include_once('widget/logo_search_cart.php'); ?>
<table width="1210" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="200" bgcolor="#B1191A" style="padding-left:10px;color:white;font:400 15px/44px 'microsoft yahei';"><a href="catalogs.php" class="to_all_catas">全部分类</a></td>
    <td>&nbsp;</td>
    <td width="187" height="44">&nbsp;</td>
  </tr>
</table>
<div style="margin:0px;padding:0px;width:100%;border:none;border-top:2px solid #B1191A;"></div>
<table width="1210"   border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="210" height="465"  valign="top" id="index_cat">
     	<?php include_once($_SERVER['DOCUMENT_ROOT'].'/widget/main_nav.php'); ?>
 	</td>
    <td height="465" align="center" valign="top" style="padding:2px 2px 0px 2px;">
		<?php include_once($_SERVER['DOCUMENT_ROOT'].'/widget/index_image_slide.php'); ?>
	</td>
    <td width="250"  valign="top" style="padding:2px 0px 0px 0px;">
	<?php include_once($_SERVER['DOCUMENT_ROOT'].'/widget/index_news_tab.php'); ?>
  </tr>
</table>
<p>&nbsp;</p>
 <?php include_once('widget/index_catalog_floor.php'); ?> 
<?php include_once('widget/footer.php'); ?>
 </body>
</html>