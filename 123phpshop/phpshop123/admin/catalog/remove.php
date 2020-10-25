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
$could_delete=1;
$colname_catalog = "-1";
$remove_succeed_url="index.php";
if (isset($_GET['id'])) {
  $colname_catalog = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_catalog = sprintf("SELECT * FROM `catalog` WHERE id = %s", $colname_catalog);
$catalog = mysql_query($query_catalog, $localhost) or die(mysql_error());
$row_catalog = mysql_fetch_assoc($catalog);
$totalRows_catalog = mysql_num_rows($catalog);
if($totalRows_catalog==0){
	$could_delete=0;
} 

mysql_free_result($catalog);

if($could_delete==1){
	$colname_catalog_children = "-1";
	if (isset($_GET['id'])) {
	  $colname_catalog_children = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
	}
	mysql_select_db($database_localhost, $localhost);
	$query_catalog_children = sprintf("SELECT * FROM `catalog` WHERE pid = %s and is_delete=0", $colname_catalog_children);
	$catalog_children = mysql_query($query_catalog_children, $localhost) or die(mysql_error());
	$row_catalog_children = mysql_fetch_assoc($catalog_children);
	$totalRows_catalog_children = mysql_num_rows($catalog_children);
	
	if($totalRows_catalog_children>0){
		$could_delete=0;
	} 
	mysql_free_result($catalog_children);
}

if($could_delete==1){

	$colname_products = "-1";
	if (isset($_GET['id'])) {
	  $colname_products = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
	}
	mysql_select_db($database_localhost, $localhost);
	$query_products = sprintf("SELECT * FROM product WHERE catalog_id = %s and is_delete=0", $colname_products);
	$products = mysql_query($query_products, $localhost) or die(mysql_error());
	$row_products = mysql_fetch_assoc($products);
	$totalRows_products = mysql_num_rows($products);
	
	if($totalRows_products>0){
		$could_delete=0;
 	} 
	
 	mysql_free_result($products);
}

if($could_delete==1){
 	
	$update_catalog = sprintf("update `catalog` set is_delete=1 where id = %s", $colname_catalog);
	$update_catalog_query = mysql_query($update_catalog, $localhost);
	if(!$update_catalog_query){
		$could_delete=0;
	}else{
		header("Location: " . $remove_succeed_url );
 	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="../../css/common_admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php if($could_delete==0){ ?>
<div class="phpshop123_infobox">
  <p>由于一下原因，您不能删除这个分类：</p>
  <p>1. 	分类不存在，请检查参数之后再试。</p>
  <p>2. 	这个分类下面还有子分类，请删除子分类之后再试。</p>
  <p>3.	这个分类下面有产品，请删除产品之后再试。</p>
  <p>4. 系统错误，无法删除，请示稍后再试。	</p>
  <p>您也可以<a href="index.php">点击这里返回</a>。    </p>
</div>
<?php } ?>
</body>
</html>
