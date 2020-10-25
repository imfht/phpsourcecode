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
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/localhost.php'); ?>
<?php
mysql_select_db($database_localhost, $localhost);
$query_ad_products = "SELECT * FROM product where is_delete=0";
$ad_products = mysql_query($query_ad_products, $localhost) or die(mysql_error());
$row_ad_products = mysql_fetch_assoc($ad_products);
$totalRows_ad_products = mysql_num_rows($ad_products);


?><style type="text/css">
<!--
.proganda_googds_title {	
	font-size: 14px;
	font-weight: bold;
}
.row_ad_products_price {
	color: #FF0000;
	font-weight: bold;
}

hr:last-child{
	border-bottom-width:0;
	
}
.ad_vertical_product_item{
	border-top:1px dotted #DEDEDE;
}

.ad_vertical_product_item:first-child{
	border-top-width:0px;
}
-->
</style>

<table width="208" border="1" cellpadding="0" cellspacing="0" bordercolor="#ddd">
  <tr>
    <td height="31" align="center" valign="middle" bgcolor="#f7f7f7"><span class="proganda_googds_title">推广商品</span></td>
  </tr>
  <tr>
    <td valign="top"><?php do { ?>
	
	<?php 
	mysql_select_db($database_localhost, $localhost);
	$query_product_images = "SELECT * FROM product_images WHERE is_delete=0 and product_id = ".$row_ad_products['id'];
	$product_images = mysql_query($query_product_images, $localhost) or die(mysql_error());
	$row_product_images = mysql_fetch_assoc($product_images);
	$totalRows_product_images = mysql_num_rows($product_images);
 	?>
        <table class="ad_vertical_product_item" style="" width="208" border="0">
          <tr>
            <td   height="110"><div align="center"><a href="/product.php?id=<?php echo $row_ad_products['id'];?>"><img src="<?php echo $row_product_images['image_files']==NULL?"/uploads/default_product.png":$row_product_images['image_files'];?>" alt="产品图片" width="100" height="100"></a></div></td>
        </tr>
          <tr>
            <td  height="18"><div align="left" style="padding-left:8px;font-size:12px;">
              <div align="center"><?php echo $row_ad_products['name']; ?></div>
            </div></td>
        </tr>
          <tr>
            <td height="16"><div align="center" class="row_ad_products_price">￥<?php echo $row_ad_products['price']; ?></div></td>
        </tr>
              </table>
        <?php } while ($row_ad_products = mysql_fetch_assoc($ad_products)); ?>
     </td>
  </tr>
</table>
<?php
mysql_free_result($ad_products);

mysql_free_result($product_images);
?>
