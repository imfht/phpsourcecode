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
$query_buy_view = "SELECT * FROM product WHERE is_on_sheft = 1 and is_delete=0 limit 6";
$buy_view = mysql_query($query_buy_view, $localhost) or die(mysql_error());
$row_buy_view = mysql_fetch_assoc($buy_view);
$totalRows_buy_view = mysql_num_rows($buy_view);
?>

<style>
table{
	border-collapse:collapse;
}
.view_buy_item{
 	border-bottom:1px dotted #ddd;
}
.view_buy_item:last-child{
 	border-bottom:1px solid #ddd;
}

.view_but_head{
	height:31px;
	width:100%;
	font-size:12px;
	background-color:#f7f7f7;
		
}
 </style>
 
   
	
 </div>
   <table width="208" border="1" cellpadding="0" cellspacing="0" bordercolor="#ddd" class="buy_view_list">
    <tr>
      <td height="31" align="center" valign="middle" bgcolor="#f7f7f7" style="font-size:12px;">浏览了该商品的用户还浏览</td>
     </tr>
  <?php do { ?>
 		<?php 
 	   	mysql_select_db($database_localhost, $localhost);
		$query_get_images = "SELECT * FROM product_images WHERE product_id =". $row_buy_view['id'];
		$get_images = mysql_query($query_get_images, $localhost) or die(mysql_error());
		$row_get_images = mysql_fetch_assoc($get_images);
		$totalRows_get_images = mysql_num_rows($get_images);
 	   ?>
	   
    <tr bgcolor="#FFFFFF" class="view_buy_item" >
      <td valign="top">
        <table width="208" height="172" >
          <tr>
            <td width="192" height="110" align="center" valign="middle"><a href="/product.php?id=<?php echo $row_buy_view['id']; ?>"><img src="<?php echo $row_get_images['image_files']==NULL?"/uploads/default_product.png":$row_get_images['image_files'];?>" width="100" height="100" /></a></td>
          </tr>
          <tr>
            <td width="192" height="18" style="font-size:12px;padding-left:8px;"><div align="center"><?php echo $row_buy_view['name']; ?></div></td>
          </tr>
          <tr height="16">
            <td ><div align="center" style="color: #FF0000">￥<?php echo $row_buy_view['price']; ?></div></td>
          </tr>
      </table>
 	  </td>
</tr>
		    <?php } while ($row_buy_view = mysql_fetch_assoc($buy_view)); ?><p>&nbsp;</p>
</table>

<?php
mysql_free_result($buy_view);
?>
