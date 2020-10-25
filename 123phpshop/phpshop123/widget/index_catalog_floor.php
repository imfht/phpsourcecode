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
$query_catlogs = "SELECT * FROM `catalog` WHERE pid = 0 and is_delete=0 ";
$catlogs = mysql_query($query_catlogs, $localhost) or die(mysql_error());
$totalRows_catlogs = mysql_num_rows($catlogs);
?>

<style type="text/css">
<!--
.STYLE3 {font-size: 12px}
.STYLE4 {
	font-size: 15px;
	color: #FF0000;
	font-weight: bold;
}
-->
</style>
<br />
<?php while($row_catlogs = mysql_fetch_assoc($catlogs)){

$query_season = "SELECT * FROM product WHERE is_delete=0 and is_on_sheft=1 and cata_path like '%|".$row_catlogs['id']."|%' limit 6";
$season = mysql_query($query_season, $localhost) or die(mysql_error());
echo $totalRows_season = mysql_num_rows($season);
 if($totalRows_season>0){
 ?>
<table style="" width="1210" border="0" align="center">
  <tr>
    <td class="index_floor_title"><a href="/product_list.php?catalog_id=<?php echo $row_catlogs['id']; ?>" ><?php echo $row_catlogs['name']; ?></a></td>
  </tr>
</table>
<table style="border-top:1px solid #c81623;"  width="1210" border="1" align="center" cellspacing="0" bordercolor="#EDEDED">
  <tr>
     <?php 
  		while ($row_season = mysql_fetch_assoc($season)){
 			mysql_select_db($database_localhost, $localhost);
			$query_get_images = "SELECT * FROM product_images WHERE product_id =". $row_season['id'];
			$get_images = mysql_query($query_get_images, $localhost) or die(mysql_error());
			$row_get_images = mysql_fetch_assoc($get_images);
			$totalRows_get_images = mysql_num_rows($get_images);
 	   ?>
    <td height="237" width="200">
		<table width="200" height="237" border="0">
      <tr>
        <td height="155" align="center"><a href="product.php?id=<?php echo $row_season['id']; ?>"><img src="<?php echo $row_get_images['image_files']==NULL?"/uploads/default_product.png":$row_get_images['image_files'];?>" width="130" height="130" border="0" /></a></td>
      </tr>
      <tr valign="top">
        <td height="36" valign="middle" style="padding-left:17px;"><span class="STYLE3"><?php echo $row_season['name']; ?></span></td>
      </tr>
      <tr>
        <td  style="padding-left:17px; height="12"><div align="left"><span class="STYLE4">￥<?php echo $row_season['price']; ?></span> </div></td>
      </tr>
    </table>
	</td>
    <?php } ?>
  </tr>
</table>
<br />
<?php }?>
<?php }?>
<?php
mysql_free_result($season);

mysql_free_result($catlogs);
?>
