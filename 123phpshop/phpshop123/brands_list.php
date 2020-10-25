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
<?php require_once('Connections/localhost.php'); 
mysql_select_db($database_localhost, $localhost);
$query_brands = "SELECT * FROM brands WHERE is_delete = 0";
$brands = mysql_query($query_brands, $localhost) or die(mysql_error());

$totalRows_brands = mysql_num_rows($brands);

$brands_array=array();
while($row_brands = mysql_fetch_assoc($brands)){
	$brands_array[]=$row_brands;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<a href="/catalogs.php"> <div class="catalog_row_item">全部商品分类</div></a>
<a href="/brands_list.php"> <div class="catalog_row_item_active">全部品牌</div></a>
</div>
 
<?php 
$total=$totalRows_brands;
$cols=6;
$row=ceil($totalRows_brands/$cols);
?>
<table width="1200" border="0" style="margin-top:10px;" align="center" cellpadding="0" cellspacing="0">
<?php for($row_i=0;$row_i<$row;$row_i++){ ?>
  <tr width="1200" align="left">
  <?php for($cols_i=0;$cols_i<$cols;$cols_i++){ ?>
    <td width="238">
	<?php $curr=$row_i*$cols_i+$cols_i;if(isset($brands_array[$curr])){ ?>
 	 <div class="product_box" align="center" style="border:1px solid #CCCCCC;margin-left:10px;">
  	 <table width="165" height="85" border="0" >
      <tr>
        <td height="54" align="center" valign="left" >
			<a href="/brands_product.php?brand_id=<?php echo  $brands_array[$curr]['id'];?>">
				<img src="<?php echo $brands_array[$curr]['image_path']==NULL?'/uploads/default_product.png':$brands_array[$curr]['image_path']; ?>" width="138" height="46">
			</a>
		</td>
      </tr>
      <tr>
        <td style="font-family:Verdana;color:#999999;font-size:12px;border-top:1px solid #CCCCCC;text-align:center;" ><?php echo $brands_array[$curr]['name']; ?></td>
      </tr>
    </table>
	 </div>
	 <?php  } ?>
	</td>
     <?php  } ?>
  </tr>
 <?php  } ?>
</table>
</body>
</html>
<?php
mysql_free_result($brands);
?>