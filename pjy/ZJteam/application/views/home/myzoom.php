<?php if(!isset($_SESSION['zjb']) || !isset($_SESSION['id'])){
	echo '<div style="z-index:1000;position:absolute;top:0;bottom:0;right:0;left:0;width:100%;height:100%;background-color:#fff;color:red;font-size:30px;text-align:center;">对不起，请登录</div>';
}?>	
<?php include 'application/views/home/header.php'?>

<div class="social_network_likes main_bg container" style="margin-top:30px;">
	<ul class="list-unstyled">
		<li><a href="<?php echo site_url('home/myzoom');?>" class="facebook-followers"><div class="followers"><p><span>我的活动</span></p></div></a></li>
		<li><a href="<?php echo site_url('home/modInfo');?>" class="dribble"><div class="followers"><p><span>修改资料</span></p></div></a></li>
		<div class="clear"> </div>
	</ul>
</div>

<div class="main_bg main_btm container" style="margin-top:20px;"><!-- start main -->
	<h3>我参与的活动</h3>
	<br>
	<br>
	<br>
</div>
<div class="webitems ">
	<div class="blog-top">
	<ul class="recomment_ul clearfix"> 
		<?php echo $actinfo;?>
	</ul>
		<div class="clear"></div>
	</div>
</div>


<?php include 'application/views/home/footer.php'?>	