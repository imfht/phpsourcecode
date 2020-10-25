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
$products_array=array();
while ($row_products_new = mysql_fetch_assoc($products)){  
	$products_array[]=$row_products_new;
}
?>
<style>
.page_nav{
	height:25px;
	width:48px;
	border:1px solid #DDD;
	color:#AAA;
	font-size:16px;
	line-height:23px;
	float:left;
 }
 
.product_box{
	border:1px solid white;
	padding:5px;
}
.product_box:hover{
	border:1px solid #e9e9e9;
}
.commented_num{
	color:#005aa0;
	font-weight:bold;
}
</style>

<?php 
$total=$totalRows_products;
$cols=4;
$row=ceil($totalRows_products/$cols);
?>
<table width="990" border="0" style="margin-top:10px;" align="center" cellpadding="0" cellspacing="0">
<?php for($row_i=0;$row_i<$row;$row_i++){ ?>
  <tr width="990" align="left">
  <?php for($cols_i=0;$cols_i<$cols;$cols_i++){ ?>
    <td width="238">
	<?php $curr=$row_i*$cols_i+$cols_i;if(isset($products_array[$curr])){ ?>
	 <?php 
   		$query_product_images = "SELECT * FROM product_images WHERE product_id = ".$products_array[$curr]['id'];
		$product_images = mysql_query($query_product_images, $localhost) or die(mysql_error());
		$row_product_images = mysql_fetch_assoc($product_images);
		$totalRows_product_images = mysql_num_rows($product_images);
 	 ?>
	 <div class="product_box" align="center">
  	 <table width="220" border="0" >
      <tr>
        <td height="225" align="center" valign="middle"><a href="/product.php?id=<?php echo  $products_array[$curr]['id'];?>"><img height="200" width="200" src="<?php echo $row_product_images['image_files']==NULL?"/uploads/default_product.png":$row_product_images['image_files'];?>"></a></td>
      </tr>
      <tr>
        <td height="30" style="font-family:Verdana;color:#FF0000;font-size:20px;">¥<?php echo  $products_array[$curr]['price'];?></td>
      </tr>
      <tr>
        <td height="26" style="font-size:12px;"><a href="/product.php?id=<?php echo  $products_array[$curr]['id'];?>"><?php echo $products_array[$curr]['name'];?></a></td>
      </tr>
      <tr>
        <td height="26" style="font-size:12px;color:#a7a7a7;">已有<span class="commented_num"><?php echo $products_array[$curr]['commented_num'];?></span>人评价</td>
      </tr>
    </table>
	 </div>
	 <?php  } ?>
	</td>
     <?php  } ?>
  </tr>
 <?php  } ?>
</table>
<div style="float:right;margin-top:20px;">
       <?php if ($pageNum_products > 0) { // Show if not first page ?>
        <a  id="pre_nav" href="<?php printf("%s?pageNum_products=%d%s", $currentPage, max(0, $pageNum_products - 1), $queryString_products); ?>"><div class="page_nav" align="center">  <  </div></a>
        <?php } // Show if not first page ?>
       <?php if ($pageNum_products < $totalPages_products) { // Show if not last page ?>
        <a   id="next_nav" href="<?php printf("%s?pageNum_products=%d%s", $currentPage, min($totalPages_products, $pageNum_products + 1), $queryString_products); ?>"><div class="page_nav" align="center"> > </div></a>
        <?php } // Show if not last page ?> 
</div>
<?php
mysql_free_result($products);
?>