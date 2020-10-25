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
﻿<?php require_once($_SERVER['DOCUMENT_ROOT'].'/Connections/localhost.php'); ?>
<?php
$colname_product_image = "-1";
if (isset($_GET['id'])) {
  $colname_product_image = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_product_image = sprintf("SELECT * FROM product_images WHERE is_delete=0 and  product_id = %s", $colname_product_image);
$product_image = mysql_query($query_product_image, $localhost) or die(mysql_error());
$row_product_image = mysql_fetch_assoc($product_image);
$totalRows_product_image = mysql_num_rows($product_image);

$colname_product_image_small = "-1";
if (isset($_GET['id'])) {
  $colname_product_image_small = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_product_image_small = sprintf("SELECT * FROM product_images WHERE is_delete=0 and product_id = %s", $colname_product_image_small);
$product_image_small = mysql_query($query_product_image_small, $localhost) or die(mysql_error());
$row_product_image_small = mysql_fetch_assoc($product_image_small);
$totalRows_product_image_small = mysql_num_rows($product_image_small);

$colname_big_images = "-1";
if (isset($_GET['id'])) {
  $colname_big_images = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_localhost, $localhost);
$query_big_images = sprintf("SELECT * FROM product_images WHERE product_id = %s", $colname_big_images);
$big_images = mysql_query($query_big_images, $localhost) or die(mysql_error());
$row_big_images = mysql_fetch_assoc($big_images);
$totalRows_big_images = mysql_num_rows($big_images);
?><script src="/js/jquery-1.7.2.min.js" ></script>
<!--[if IE 6]> 
<script src="/widget/product_image_slide/js/iepng.js" type="text/javascript"></script> 
<script type="text/javascript">
   EvPNG.fix('div, ul, img, li, input,a,span');  
</script>
<![endif]-->
<style type="text/css">
*{ margin:0; padding:0; list-style:none;}
img{ border:0;}
 .ban{ width:350px; height:429px; position:relative; overflow:hidden;margin:0 auto 0 auto;}
.ban2{ width:350px; height:350px; position:relative; overflow:hidden;}
.ban2 ul{ position:absolute; left:0; top:0;}
.ban2 ul li{ width:350px; height:350px;}
.prev{ float:left; cursor:pointer;}
.num{ height:82px;overflow:hidden; width:304px; position:relative;float:left;}
.min_pic{ padding-top:10px; width:350px;}
.num ul{ position:absolute; left:0; top:0;}
.num ul li{ width:54px; height:54px; margin-right:5px; padding:1px;}
.num ul li.on{ border:1px solid red; padding:0;}
.prev_btn1{display:table-cell;vertical-align:middle; text-align:center; height:18px;margin-right:10px; cursor:pointer; float:left;}
.next_btn1{display:table-cell;vertical-align:middle;  text-align:center; height:18px;cursor:pointer;float:right;}
.prev1{ position:absolute; top:220px; left:20px; width:28px; height:51px;z-index:9;cursor:pointer;}
.next1{ position:absolute; top:220px; right:20px; width:28px; height:51px;z-index:9;cursor:pointer;}
.mhc{ background:#000; width:100%;opacity:0.5;-moz-opacity:0.5;filter:alpha(Opacity=50); position:absolute; left:0; top:0; display:none;}
.pop_up{ width:350px; height:350px; padding:10px; background:#fff; position:fixed; -position:absolute; left:50%; top:50%; margin-left:-255px; margin-top:-255px; display:none; z-index:99;}
.pop_up_xx{ width:40px; height:40px; position:absolute; top:-40px; right:0; cursor:pointer;}
.pop_up2{ width:350px; height:350px; position:relative; overflow:hidden;}
.pop_up2{ width:350px; height:350px; position:relative; overflow:hidden; float:left;}
.pop_up2 ul{ position:absolute; left:0; top:0;}
.pop_up2 ul li{ width:3500px; height:350px; float:left;}
</style>
<!-- 代码begin -->
<div class="ban" id="demo1">
	<div class="ban2" id="ban_pic1">
 		<ul>
		<?php if($totalRows_product_image>0){ ?>
	      <?php do { ?>
	        <li><a href="javascript:;"><img width="350" height="350"  src="<?php echo $row_product_image['image_files']; ?>" alt="" onclick="return false;"/></a></li>
	        <?php } while ($row_product_image = mysql_fetch_assoc($product_image)); ?>
			
			<?php }else{ ?>
			
					        <li><a href="javascript:;"><img width="350" height="350"  src="/uploads/default_product.png" alt=""/></a></li>
 			<?php } ?>
			</ul>
	</div>
	<div class="min_pic" >
		<div class="prev_btn1" valign="middle" id="prev_btn1" style="height:56px;"><img style="margin-top:20px;" src="/widget/product_image_slide/images/feel3.png" width="9" height="18"  alt=""/></div>
		<div class="num clearfix" id="ban_num1">
			<ul>
			<?php if($totalRows_product_image_small>0){ ?>
			 <?php do { ?>
				<li><a href="javascript:;"><img src="<?php echo $row_product_image_small['image_files']; ?>" width="54" height="54" alt=""/></a></li>
 				<?php } while ($row_product_image_small = mysql_fetch_assoc($product_image_small)); ?>
 				<?php  }else{ ?>
 								<li><a href="javascript:;"><img src="/uploads/default_product.png" width="54" height="54" alt=""/></a></li>
 				<?php } ?>
				
				</ul>
 			</ul>
		</div>
		<div class="next_btn1" style="height:56px;" id="next_btn1"><img src="/widget/product_image_slide/images/feel4.png" width="9" height="18" style="margin-top:20px;" alt=""/></div>
	</div>
</div>

<!--div class="mhc"></div>

<div class="pop_up" id="demo2">
	<div class="pop_up_xx"><img src="/widget/product_image_slide/images/chacha3.png" width="40" height="40"  alt=""/></div>
	<div class="pop_up2" id="ban_pic2">
		<div class="prev1" id="prev2" ><img  src="/widget/product_image_slide/images/index_tab_l.png" width="28" height="51"  alt=""/></div>
		<div class="next1" id="next2"><img src="/widget/product_image_slide/images/index_tab_r.png" width="28" height="51"  alt=""/></div>
		<ul>
          <?php do { ?>
            <li><a href="javascript:;"><img src="<?php echo $row_big_images['image_files']; ?>" width="350" height="350" alt=""/></a></li>
            <?php } while ($row_big_images = mysql_fetch_assoc($big_images)); ?></ul>
	</div>
</div-->
<script src="/widget/product_image_slide/js/pic_tab.js"></script>
<script type="text/javascript">
jq('#demo1').banqh({
	box:"#demo1",//总框架
	pic:"#ban_pic1",//大图框架
	pnum:"#ban_num1",//小图框架
	prev_btn:"#prev_btn1",//小图左箭头
	next_btn:"#next_btn1",//小图右箭头
	pop_prev:"#prev2",//弹出框左箭头
	pop_next:"#next2",//弹出框右箭头
	prev:"#prev1",//大图左箭头
	next:"#next1",//大图右箭头
	pop_div:"#demo2",//弹出框框架
	pop_pic:"#ban_pic2",//弹出框图片框架
	pop_xx:".pop_up_xx",//关闭弹出框按钮
	mhc:".mhc",//朦灰层
	autoplay:true,//是否自动播放
	interTime:5000,//图片自动切换间隔
	delayTime:400,//切换一张图片时间
	pop_delayTime:400,//弹出框切换一张图片时间
	order:0,//当前显示的图片（从0开始）
	picdire:true,//大图滚动方向（true为水平方向滚动）
	mindire:true,//小图滚动方向（true为水平方向滚动）
	min_picnum:5,//小图显示数量
	pop_up:true//大图是否有弹出框
})
</script>
<!-- 代码end -->
<?php
mysql_free_result($product_image);

mysql_free_result($product_image_small);

mysql_free_result($big_images);
?>
