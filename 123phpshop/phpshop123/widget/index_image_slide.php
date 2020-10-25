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
$maxRows_ad_one = 10;
$pageNum_ad_one = 0;
if (isset($_GET['pageNum_ad_one'])) {
  $pageNum_ad_one = $_GET['pageNum_ad_one'];
}
$startRow_ad_one = $pageNum_ad_one * $maxRows_ad_one;

mysql_select_db($database_localhost, $localhost);
$query_ad_one = "SELECT * FROM ad_images WHERE ad_id = 1";
$query_limit_ad_one = sprintf("%s LIMIT %d, %d", $query_ad_one, $startRow_ad_one, $maxRows_ad_one);
$ad_one = mysql_query($query_limit_ad_one, $localhost) or die(mysql_error());
$row_ad_one = mysql_fetch_assoc($ad_one);

if (isset($_GET['totalRows_ad_one'])) {
  $totalRows_ad_one = $_GET['totalRows_ad_one'];
} else {
  $all_ad_one = mysql_query($query_ad_one);
  $totalRows_ad_one = mysql_num_rows($all_ad_one);
}
$totalPages_ad_one = ceil($totalRows_ad_one/$maxRows_ad_one)-1;

?>
<link rel="stylesheet" href="/js/image_slide/css/responsiveslides.css">
<link rel="stylesheet" href="/js/image_slide/css/style.css">
<div id="wrapper">
	<div class="callbacks_container">
		<ul class="rslides" id="slider4">
			<?php do { ?>
              <li> <a href="<?php echo $row_ad_one['link_url'];?>" target="_blank"><img src="<?php echo $row_ad_one['image_path'];?>" style="height:465px !important"  alt=""></a></li>
			  <?php } while ($row_ad_one = mysql_fetch_assoc($ad_one)); ?></ul>
	</div>
</div>

<script language="JavaScript" type="text/javascript" src="/js/image_slide/js/jquery-1.8.3.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/image_slide/js/responsiveslides.min.js"></script>
<script>
$(function () {

 	$("#slider4").responsiveSlides({
	auto: false,
	pager: false,
	nav: true,
	speed: 500,
	height:521,
	namespace: "callbacks",
 
});

});
</script>
  
<?php 

mysql_free_result($ad_one);

?>