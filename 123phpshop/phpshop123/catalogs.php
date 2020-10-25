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
mysql_select_db($database_localhost, $localhost);
$query_top_catalogs = "SELECT * FROM `catalog` WHERE pid = 0";
$top_catalogs = mysql_query($query_top_catalogs, $localhost) or die(mysql_error());
$row_top_catalogs = mysql_fetch_assoc($top_catalogs);
$totalRows_top_catalogs = mysql_num_rows($top_catalogs);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>全部分类</title>
<style>
<br />
*{
	font-family:"Microsoft yahei";
}

table{
	border-collapse:collapse;
}
a{
	text-decoration:none;
}

.catalog_row{
	width:1210px;
	margin:20px auto;
	height:31px;
	border-bottom:2px solid #E13335;
}


.catalog_row_item_active{
	width:122px;
	height:31px;
	text-align:center;
	line-height:30px;
	background-color:#E13335;
	color:#ffffff;
	font-size:14px;
	font-weight:bold;
	margin-left:20px;
	border-top-right-radius:5px;
	border-top-left-radius:5px;
	float:left;
}

.catalog_row_item{
	width:122px;
	height:30px;
	text-align:center;
	line-height:30px;
	background-color:#f8f8f8;
	color:#000000;
	border:1px solid #E4E4E4;
	font-size:14px;
	font-weight:bold;
	margin-left:20px;
	border-top-right-radius:5px;
	border-top-left-radius:5px;
	border-bottom:2px solid #ca0401;
	float:left;
}
.first_level a{color:#666;font-size: 14px;font-family:"Microsoft yahei";line-height:19px;font-weight:bold;padding-left:20px;}
.second_level{border-bottom:1px dotted #cccccc;}
.second_level:last-child{border-bottom-width:0px;text-align:right;}
.second_level_link{color:#CC0000;font-weight:bold;font-size:12px;}
.third_level_link{font-size:12px;padding-left:16px;color:#666;}
 
</style>
</head>

<body style="margin:0px;">
<?php include_once('/widget/top_full_nav.php'); ?>
<?php include_once('/widget/logo_search_cart.php'); ?>
<?php include_once('/widget/full_ori_nav_1210.php'); ?>
<div class="catalog_row">
  <a href="/catalogs.php"> <div class="catalog_row_item_active">全部商品分类</div></a>
<a href="/brands_list.php"> <div class="catalog_row_item">全部品牌</div></a>
</div>
<?php if($totalRows_top_catalogs>0){ ?>
<?php do { ?>
  <table width="1210" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#E9E9E9" style="border:1px solid #E9E9E9;border-collapse:collapse;margin-bottom:10px;">
    <tr>
      <td width="1183" height="29" bordercolor="#E9E9E9" bgcolor="#f9f9f9"><span class="first_level"><a href="product_list.php?catalog_id=<?php echo $row_top_catalogs['id']; ?>"><?php echo $row_top_catalogs['name']; ?></a></span></td>
    </tr>
    <tr>
      <td>
	    <div align="center">
	      <?php 
 	mysql_select_db($database_localhost, $localhost);
	$query_second_level_cata = "SELECT * FROM `catalog` WHERE pid = ".$row_top_catalogs['id'];
	$second_level_cata = mysql_query($query_second_level_cata, $localhost) or die(mysql_error());
 	$totalRows_second_level_cata = mysql_num_rows($second_level_cata);
	if($totalRows_second_level_cata>0){
	while($row_second_level_cata = mysql_fetch_assoc($second_level_cata)){
?>
	      <table width="99%" border="0" cellpadding="0" cellspacing="0" class="second_level">
	        <tr>
	          <td width="65" height="34"><div align="right" style="width:56px;"><a href="product_list.php?catalog_id=<?php echo $row_second_level_cata['id']; ?>" class="second_level_link"><?php echo $row_second_level_cata['name']; ?></a></div></td>
            <td height="34">
              <?php 
 	  mysql_select_db($database_localhost, $localhost);
	$query_third_level_cata = "SELECT * FROM `catalog` WHERE pid = ".$row_second_level_cata['id'];
	$third_level_cata = mysql_query($query_third_level_cata, $localhost) or die(mysql_error());
 	$totalRows_third_level_cata = mysql_num_rows($third_level_cata);
	if($totalRows_third_level_cata>0){
	while($row_third_level_cata = mysql_fetch_assoc($third_level_cata)){
?>
              <a href="product_list.php?catalog_id=<?php echo $row_second_level_cata['id']; ?>" class="third_level_link"><?php echo $row_second_level_cata['name']; ?></a>
              <?php }?>
              <?php }?>              </td>
          </tr>
          </table>
	      <?php }?>
	      <?php }?>
        </div></td>
    </tr>
      </table>
  <?php } while ($row_top_catalogs = mysql_fetch_assoc($top_catalogs)); ?>
  
  <?php } ?>
  <p>&nbsp;</p>
</body>
</html>