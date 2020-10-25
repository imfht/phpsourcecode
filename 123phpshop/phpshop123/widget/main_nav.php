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
$query_top_catas = "SELECT * FROM `catalog` WHERE pid = 0";
$top_catas = mysql_query($query_top_catas, $localhost) or die(mysql_error());
$row_top_catas = mysql_fetch_assoc($top_catas);
$totalRows_top_catas = mysql_num_rows($top_catas);
if($totalRows_top_catas==0){
	return;
}
?>
<style type="text/css">
<!--
.top_cata_text {font-size: 14px;line-height:31px;}
.top_cata_row:hover{background-color:#ffffff;color:#b61d1d;border-right:1px solid #b61d1d; border-left:1px solid #b61d1d;}
.top_cata_row{padding-left:10px;}
-->
</style>
   <?php do { ?>
   <a href="../product_list.php?catalog_id=<?php echo $row_top_catas['id']; ?>" class="top_cata_text">
   <div class="top_cata_row"><?php echo $row_top_catas['name']; ?><div style="width:4px;line-height:31px;float:right;font-family:consolas;padding-right:14px;">></div></div></a>
    <?php } while ($row_top_catas = mysql_fetch_assoc($top_catas)); ?>
 <?php
mysql_free_result($top_catas);
?>

