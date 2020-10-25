<!DOCTYPE HTML>
<html>
<head>
<title>支教邦</title>
<!-- Bootstrap -->
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta name="copyright" content="<?php echo $set->copyright; ?>" />
<meta name="keyword" content="<?php echo $set->keywords; ?>" />
<meta name="description" content="<?php echo $set->description;?>">
<link href="<?php echo base_url()?>Public/css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="<?php echo base_url()?>Public/css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="<?php echo base_url()?>Public/css/home.css" rel='stylesheet' type='text/css' />
 <!--[if lt IE 9]>
     <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
     <script src="https://oss.maxcdn.com/libs/respond.<?php echo base_url()?>Public/js/1.4.2/respond.min.js"></script>
<![endif]-->
<link href="<?php echo base_url()?>Public/css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- start plugins -->
<script type="text/javascript" src="<?php echo base_url()?>Public/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>Public/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>Public/js/bootstrap.min.js"></script>
<!-- start slider -->
<link href="<?php echo base_url()?>Public/css/slider.css" rel="stylesheet" type="text/css" media="all" />

<script type="text/javascript" src="<?php echo base_url()?>Public/js/modernizr.custom.28468.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>Public/js/jquery.cslider.js"></script>
	<script type="text/javascript">
			$(function() {

				$('#da-slider').cslider({
					autoplay : true,
					bgincrement : 450
				});

			});
		</script>
<!-- Owl Carousel Assets -->
<link href="<?php echo base_url()?>Public/css/owl.carousel.css" rel="stylesheet">
<script src="<?php echo base_url()?>Public/js/owl.carousel.js"></script>
		<script>
			$(document).ready(function() {

				$("#owl-demo").owlCarousel({
					items : 4,
					lazyLoad : true,
					autoPlay : true,
					navigation : true,
					navigationText : ["", ""],
					rewindNav : false,
					scrollPerPage : false,
					pagination : false,
					paginationNumbers : false,
				});

			});
		</script>
		<!-- //Owl Carousel Assets -->
<!----font-Awesome----->
   	<link rel="stylesheet" href="<?php echo base_url()?>Public/fonts/css/font-awesome.min.css">
<!----font-Awesome----->

<link rel="stylesheet" href="<?php echo base_url()?>Public/css/footer.css">
<style>
.container{width:1050px;}
.common-footer{width:1050px!important; margin:0 auto;}
</style>
</head>
<body>
<div class="header_bg">
<div class="container">
	<div class="row header">
		<div class="logo navbar-left">
			<h1>
				<img src="<?php echo base_url()?>Public/images/logo.jpg" width="38" style="font-family:宋体; float:left"></img>
				<a href="index.html" style="font-family:宋体; float:left">支教邦</a>
			</h1>
		</div>
		
		<div class="clearfix"></div>
	</div>
</div>
</div>
<div class="container">
	<div class="row h_menu">
		<nav class="navbar navbar-default navbar-left" role="navigation">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		    </div>
		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav">
		        <li id="index"><a href="<?php echo site_url('home/index');?>">主页</a></li>
				<li id="zhijiaolist"><a href="<?php echo site_url('home/zhijiaolist');?>">支教活动</a></li>
				<li id="zclist"><a href="<?php echo site_url('home/zclist');?>">爱心众筹</a></li>
				<li id="booklist"><a href="<?php echo site_url('home/booklist');?>">捐书捐物</a></li>
				<li id="safelist"><a href="<?php echo site_url('home/safelist');?>">保险基金</a></li>
				<li id="yougan"><a href="<?php echo site_url('home/youganList');?>">支教有感</a></li>
		        <li id="about"><a href="<?php echo site_url('home/about');?>">关于我们</a></li>
		      </ul>
		    </div><!-- /.navbar-collapse -->
		    <!-- start soc_icons -->
		</nav>
		<div class="soc_icons navbar-right">
		<?php if(isset($_SESSION['zjb'])) { ?>
			<ul class="list-unstyled text-center">
				<li><a href="<?php echo site_url("home/myzoom");?>">管理</a></li>
				<li><a href="<?php echo site_url("home/logout");?>">退出</a></li>
			</ul>	
		<?php }else { ?>
			<ul class="list-unstyled text-center">
				<li><a href="<?php echo site_url("home/logindex");?>">登录</a></li>
				<li><a href="<?php echo site_url("home/register");?>">注册</a></li>
			</ul>			
		<?php }?>
		</div>
	</div>
</div>

