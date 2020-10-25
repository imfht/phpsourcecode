<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $hd['config']['WEBNAME'];?> - <?php echo $hd['config']['SEO_TITLE'];?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href='http://fonts.useso.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
<link href="Theme/Default/css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="Theme/Default/css/magnific-popup.css">
<script src="Theme/Default/js/jquery.min.js"></script>
</head>
<body>
<!-- start header -->
<style type="text/css">
	.header_bg {
		height: 98px;
	}
	#topnav .logo {
		padding: 1em 0em;
	}
	#topnav .logo img {
		height: 60px;
	}
</style>
<div class="header_bg">
<div class="wrap">
	<div id="content">
      <header id="topnav">
      	<nav>
      		<ul>
      			<li class="active"><a class="scroll" href="#content">首页</a></li>
      			<li><a class="scroll" href="#">程序下载</a></li>
      			<li><a class="scroll" href="#">程序演示</a></li>
				<li><a class="scroll"href="#service">功能概述</a></li>
				<li><a class="scroll" href="#portfolio">移动解决方案</a></li>
				<li><a class="scroll" href="#portfolio">案例</a></li>
				<li><a class="scroll" href="#guanyuwm">关于我们</a></li>
				<li><a  class="scroll" href="#contact">联系我们</a></li>
				<div class="clear"> </div>
			</ul>
        </nav>
         <div class="logo"><a href="<?php echo U('index');?>"><img src="Theme/Default/images/logo.png"></a></div>
        <a href="#" id="navbtn">Nav Menu</a>
        <div class="clear"> </div>
      </header><!-- @end #topnav -->
      <script type="text/javascript"  src="Theme/Default/js/menu.js"></script>
    </div>
</div>
</div>
<!--start-slider---->
<div class="slider" id="home"> 
				<div class="wrap">
				<!---start-da-slider----->
				<div id="da-slider" class="da-slider">
				<div class="da-slide">
					<h2>互联网+</h2>
					<p>“互联网+”是创新2.0下的互联网与传统行业融合发展的新形态、新业态。</p>
					<a href="#" class="da-link">了解更多</a>
				</div>
				<div class="da-slide">
					<h2>拒绝花瓶</h2>
					<p>探索友好、有用的品牌移动营销解决方案</p>
					<a href="#" class="da-link">了解更多</a>
				</div>
				<div class="da-slide">
					<h2>人类正从IT时代走向DT时代</h2>
					<p>“人类正从IT时代走向DT时代”。那么到底什么是DT，与IT有什么不一样呢？</p>
					<a href="#" class="da-link">了解更多</a>
				</div>
				<nav class="da-arrows">
					<span class="da-arrows-prev"></span>
					<span class="da-arrows-next"></span>
				</nav>
			</div>
			<link rel="stylesheet" type="text/css" href="Theme/Default/css/slider.css" />
			<script type="text/javascript" src="Theme/Default/js/modernizr.custom.28468.js"></script>
			<script type="text/javascript" src="Theme/Default/js/jquery.cslider.js"></script>
			<script type="text/javascript">
				$(function() {
				
					$('#da-slider').cslider({
						autoplay	: true,
						bgincrement	: 450
					});
				
				});
			</script>
				<!---//End-da-slider----->
			</div>
</div>
<!-----------service------------>
<div  class="sevice" id="service">
	<div class="wrap">
		<div class="service-grids">
						<div class="images_1_of_4">
							 <img src="Theme/Default/images/round4.png">
							 <h3>视频上传转码 +</h3>
							 <p>“互联网+”是创新2.0下的互联网与传统行业融合发展的新形态、新业态，是知识社会创新2.0推动下的互联网形态演进及其催生的经济社会发展新形态。</p>
						    <!-- <div class="button"><span><a href="#">Read More</a></span></div>-->
						</div>
						<div class="images_1_of_4">
					 		<img src="Theme/Default/images/round1.png">
					 		<h3>视频云储存加速</h3>
					 		<p>品牌是给拥有者带来溢价、产生增值的一种无形的资产，他的载体是用以和其他竞争者的产品或劳务相区分的名称、术语、象征、记号或者设计及其组合。增值的源泉来自于消费者心智中形成的关于其载体的印象。 </p>
				     		<!--<div class="button"><span><a href="#">Read More</a></span></div>-->
						</div>
						<div class="images_1_of_4">
							 <img src="Theme/Default/images/round2.png">
							 <h3>PC端播放</h3>
							 <p>互联网作为唯一一种全天候媒体平台是传统媒体可望不可及的。在互联网上建立自己的网站，最显而易见的就是可以向世界展示自己的企业风采，让更多人了解自己的企业，使企业能够在公众知名度上有一定的提升</p>
						     <!--<div class="button"><span><a href="#">Read More</a></span></div>-->
						</div>
						<div class="images_1_of_4">
							 <img src="Theme/Default/images/round3.png">
							 <h3>移动端播放</h3>
							 <p>移动互联网就是将移动通信和互联网二者结合起来，成为一体。在最近几年里，移动通信和互联网成为当今世界发展最快、市场潜力最大的两大业务，移动互联网可以预见将会创造怎样的经济神话。</p>
						     <!--<div class="button"><span><a href="#">Read More</a></span></div>-->
						</div>
						<div class="clear"> </div>
		 </div>
</div>
<div class="portfolio_main"id ="portfolio">
				<div class="wrap">
					<div class="heading_h">
						<h3><a href="#">移动解决方案</a></h3>
					</div>
					<!--start-mfp -->	
				<div id="small-dialog1" class="mfp-hide">
					<div class="pop_up">
							   <img src="Theme/Default/images/pop1.jpg">
							   	 <h2>Lorem ipsum </h2>
								 <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								 <p class="pop_p">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								</div>
					
				</div>
				 <div id="small-dialog2" class="mfp-hide">
							   <div class="pop_up2">
							   <img src="Theme/Default/images/pop2.jpg">
							   	 <h2>Lorem ipsum </h2>
								 <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								 <p class="pop_p">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								</div>
				 </div>
				 <div id="small-dialog3" class="mfp-hide">
							   <div class="pop_up3">
							   	<img src="Theme/Default/images/pop3.jpg">
							   	 <h2>Lorem ipsum </h2>
								 <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								 <p class="pop_p">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								</div>
				 </div>
				 <div id="small-dialog4" class="mfp-hide">
							   <div class="pop_up4">
							   	<img src="Theme/Default/images/pop4.jpg">
							   	 <h2>Lorem ipsum </h2>
								 <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								 <p class="pop_p">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								</div>
				 </div>
				 <div id="small-dialog5" class="mfp-hide">
							   <div class="pop_up5">
							   	<img src="Theme/Default/images/pop5.jpg">
							   	 <h2>Lorem ipsum </h2>
								 <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								 <p class="pop_p">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								</div>
				 </div>
				 <div id="small-dialog6" class="mfp-hide">
							   <div class="pop_up6">
							   	<img src="Theme/Default/images/pop6.jpg">
							   	 <h2>Lorem ipsum </h2>
								 <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								 <p class="pop_p">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								</div>
				 </div>
				 <div id="small-dialog7" class="mfp-hide">
							   <div class="pop_up7">
							   	 <h2>Lorem ipsum </h2>
								 <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								 <p class="pop_p">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie.</p>
								</div>
				 </div>
				<!--end-mfp -->	
				<!--start-content-->
			<div class="gallery">
			<div class="container">
				<div id="gallerylist">
				<div class="gallerylist-wrapper">				
					<a class="popup-with-zoom-anim" href="#small-dialog1">
						<img src="Theme/Default/images/gd1.jpg" alt="Image 1"/
					</a>
				</div>
				<div class="gallerylist-wrapper">				
					<a class="popup-with-zoom-anim" href="#small-dialog2">
						<img src="Theme/Default/images/gd2.jpg"  alt="Image 1"/
					</a>
				</div>
				<div class="gallerylist-wrapper">				
					<a class="popup-with-zoom-anim" href="#small-dialog3">
						<img src="Theme/Default/images/gd3.jpg"  alt="Image 1"/>
			
					</a>
				</div>
				<div class="clear"></div>
			</div>		
			<div id="gallerylist1">
				
			</div>																																					
		</div>
	</div>
	<!--end container -->
				<!--<script src="Theme/Default/js/jquery.magnific-popup.js" type="text/javascript"></script>
				<script>
					$(document).ready(function() {
						$('.popup-with-zoom-anim').magnificPopup({
							type: 'inline',
							fixedContentPos: false,
							fixedBgPos: true,
							overflowY: 'auto',
							closeBtnInside: true,
							preloader: false,
							midClick: true,
							removalDelay: 300,
							mainClass: 'my-mfp-zoom-in'
					});
				});
				</script>
				-->
		</div>
	</div>
</div>
<!-----------//portfolio//----------->
<!-----------start-team-------------->
<div class="team" id="team" style="display: none;">
				 <div class="wrap">
				 		<div class="heading_h">
							<h3><a href="#">The Team</a></h3>
						</div>	
				 		<div class="middle-grids">
							<div class="grid_1_of_4 images_1_of_4">
					 			<a class="popup-with-zoom-anim" href="#small-dialog7">
					 				<span class="rollover"> </span>
								</a>
								<img src="Theme/Default/images/pic4.jpg"  alt="Image 1"/>
					 			<h3>Adrian Thomas </h3>
					 			<h4>CEO&Founder</h4>
					 			<p>Lorem ipsum is simply dummy text of the printing and typesetting industry.Lorem ipsum has been </p>
							</div>
							<div class="grid_1_of_4 images_1_of_4">
								<a class="popup-with-zoom-anim" href="#small-dialog7">
					 				<span class="rollover"> </span>
								</a>
								 <img src="Theme/Default/images/pic2.jpg">
								 <h3>Narate Ketram</h3>
								 <h4>Creative Diector</h4>
								 <p>Lorem ipsum is simply dummy text of the printing and typesetting industry.Lorem ipsum has been</p>
							</div>
							<div class="grid_1_of_4 images_1_of_4">
								<a class="popup-with-zoom-anim" href="#small-dialog7">
					 				<span class="rollover"> </span>
								</a>
								 <img src="Theme/Default/images/pic3.jpg">
							 	<h3>Fernando Comet</h3>
							 	<h4>CEO&Founder</h4>
							 	<p>Lorem ipsum is simply dummy text of the printing and typesetting industry.Lorem ipsum has been </p>
						    
							</div>
							<div class="grid_1_of_4 images_1_of_4">
								<a class="popup-with-zoom-anim" href="#small-dialog7">
					 				<span class="rollover"> </span>
								</a>
								 <img src="Theme/Default/images/pic1.jpg">
								 <h3>Adedayo Saheed</h3>
								 <h4>Creative Diector</h4>
								 <p>Lorem ipsum is simply dummy text of the printing and typesetting industry.Lorem ipsum has been</p>
							 </div>
							 <div class="clear"> </div>
					</div>
		 		</div>
</div>
<!----------end-team----------------->
<!-----------start-pricing----------->
<!--<div class="pricing-plans">
					<div class="wrap">
						<div class="pricing-grids">
						<div class="pricing-grid">
							<h3><a href="#">Basic</a></h3>
							<div class="price-value">
								<a href="#">$5.01/month</a>
							</div>
							<ul>
								<li><a href="#">Lorem ipsum</a></li>
								<li><a href="#">Dolor sitamet, Consect</a></li>
								<li><a href="#">Adipiscing elit</a></li>
								<li><a href="#">Proin commodo turips</a></li>
								<li><a href="#">Laws pulvinarvel</a></li>
								<li><a href="#">Prnare nisi pretium</a></li>
							</ul>
							<div class="cart">
								<a class="popup-with-zoom-anim" href="#small-dialog">Buy now</a>
							</div>
						</div>
						<div class="pricing-grid">
							<h3><a href="#">Pro</a></h3>
							<div class="price-value">
								<a href="#">$10.01/month</a>
							</div>
							<ul>
								<li><a href="#">Lorem ipsum</a></li>
								<li><a href="#">Dolor sitamet, Consect</a></li>
								<li><a href="#">Adipiscing elit</a></li>
								<li><a href="#">Proin commodo turips</a></li>
								<li><a href="#">Laws pulvinarvel</a></li>
								<li><a href="#">Prnare nisi pretium</a></li>
							</ul>
							<div class="cart">
								<a class="popup-with-zoom-anim" href="#small-dialog">Buy now</a>
							</div>
						</div>
						<div class="pricing-grid">
							<h3><a href="#">Premium</a></h3>
							<div class="price-value">
								<a href="#">$20.01/month</a>
							</div>
							<ul>
								<li><a href="#">Lorem ipsum</a></li>
								<li><a href="#">Dolor sitamet, Consect</a></li>
								<li><a href="#">Adipiscing elit</a></li>
								<li><a href="#">Proin commodo turips</a></li>
								<li><a href="#">Laws pulvinarvel</a></li>
								<li><a href="#">Prnare nisi pretium</a></li>
							</ul>
							<div class="cart">
								<a class="popup-with-zoom-anim" href="#small-dialog">Buy now</a>
							</div>
						</div>
							<div class="clear"> </div>
							<!-----pop-up-grid---->
								 <!--<div id="small-dialog" class="mfp-hide">
									<div class="pop_up">
									 	<div class="payment-online-form-left">
											<form>
												<h4><span class="shipping"> </span>Shipping</h4>
												<ul>
													<li><input class="text-box-dark" type="text" value="Frist Name" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Frist Name';}"></li>
													<li><input class="text-box-dark" type="text" value="Last Name" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Last Name';}"></li>
												</ul>
												<ul>
													<li><input class="text-box-dark" type="text" value="Email" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Email';}"></li>
													<li><input class="text-box-dark" type="text" value="Company Name" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Company Name';}"></li>
												</ul>
												<ul>
													<li><input class="text-box-dark" type="text" value="Phone" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Phone';}"></li>
													<li><input class="text-box-dark" type="text" value="Address" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Address';}"></li>
													<div class="clear"> </div>
												</ul>
												<div class="clear"> </div>
											<ul class="payment-type">
												<h4><span class="payment"> </span> Payments</h4>
												<li><span class="col_checkbox">
													<input id="3" class="css-checkbox1" type="checkbox">
													<label for="3" name="demo_lbl_1" class="css-label1"> </label>
													<a class="visa" href="#"> </a>
													</span>
													
												</li>
												<li>
													<span class="col_checkbox">
														<input id="4" class="css-checkbox2" type="checkbox">
														<label for="4" name="demo_lbl_2" class="css-label2"> </label>
														<a class="paypal" href="#"> </a>
													</span>
												</li>
												<div class="clear"> </div>
											</ul>
												<ul>
													<li><input class="text-box-dark" type="text" value="Card Number" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Card Number';}"></li>
													<li><input class="text-box-dark" type="text" value="Name on card" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Name on card';}"></li>
													<div class="clear"> </div>
												</ul>
												<ul>
													<li><input class="text-box-light hasDatepicker" type="text" id="datepicker" value="Expiration Date" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Expiration Date';}"><em class="pay-date"> </em></li>
													<li><input class="text-box-dark" type="text" value="Security Code" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Security Code';}"></li>
													<div class="clear"> </div>
												</ul>
												<ul class="payment-sendbtns">
													<li><input type="reset" value="Cancel"></li>
													<li><input type="submit" value="Process order"></li>
												</ul>
												<div class="clear"> </div>
											</form>
										</div>
						  			</div>
								</div>
							</div>
						<div class="clear"> </div>
					</div>
				</div>
			</div>-->
			<!-- Add fancyBox light-box -->
				<!--<script>
					$(document).ready(function() {
						$('.popup-with-zoom-anim').magnificPopup({
							type: 'inline',
							fixedContentPos: false,
							fixedBgPos: true,
							overflowY: 'auto',
							closeBtnInside: true,
							preloader: false,
							midClick: true,
							removalDelay: 300,
							mainClass: 'my-mfp-zoom-in'
					});
				});
		</script>-->
		<!----End-pricingplans---->
<!-----------end-pricing------------->
<!---------text-slider------------->
			
		 <div class="wmuSlider example1" id="guanyuwm">
		 	<div class="wrap">
			   <article style="position: absolute; width:64%; opacity: 0;"> 
				   	
					<div class="cont span_2_of_3" style="padding-bottom: 100px;">
						    <h4 style="font-family: '微软雅黑';">关于我们</h1>
							<p style="font-family: '微软雅黑';text-align: left;text-indent:36px">微梦想文化传媒有限公司是江苏省一家专业提供全端互联网行业解决方案的科技公司。成立于2014年1月，以技术创新促进企业互联网渠道发展经营为使命，致力于为企业提供专业的一体化、经济功能型、多平台融合企业互联网经济解决方案。</p>
<p style="font-family: '微软雅黑';text-align: left;text-indent:36px"">Md公司拥有互联网商务平台研发团队和客户服务团队，以徐州城市为中心辐射全国市场，为企业提供售前售后一体化互联网服务。目前Md提供的产品与服务涉及金融业、制造业、电子商务、旅游、婚嫁、教育培训等各个行业，以优质的产品和服务获得广大企业用户及行业的高度认可。</p>
<p style="font-family: '微软雅黑';text-align: left;text-indent:36px"">经过几年的创新与积累，我们现拥有一系列成功的互联网产品解决方案：Ideasoft solution。</p>
					</div>
					<div class="clear">
						
					</div>
				</article>
		 
                  <script src="Theme/Default/js/jquery.wmuSlider.js"></script> 
					<script>
       				     $('.example1').wmuSlider();         
   					</script> 	           	      
	         </div>
	     </div>
<!----------//text-slider------------>
<!--------start-contact-----------> 
 <div class="contact" id="contact">
	<div class="wrap">
		<h2>联系我们</h2>
			<div class="contact-form">
				<div class="para-contact">
					<h4>与我们取得联系</h4>
					<!--<p>Lorem ipsum is simply dummy text of the printing and typesetting industry.Lorem ipsum has been the printing and typesetting industry.</p>-->
				
				 	<!--<div class="social_2 social_3">	
			           <ul>	
						    <li class="facebook"><a href="#"><span> </span></a></li>
						    <li class="twitter"><a href="#"><span> </span></a></li>	 	
							<li class="google"><a href="#"><span> </span></a></li>
					  </ul>
		 		  </div>-->
		 		  <div class="get-intouch-left-address">
						<p>Guizhou South Road two Xixiu District of city of Anshun Province</p>
						<p><?php echo $hd['config']['ADDRESS'];?></p>
						<p><?php echo $hd['config']['MOBILE'];?></p>
						<p><?php echo $hd['config']['EMAIL'];?></p>
					</div>
					<div class="clear"> </div>	
				</div>
						<div class="form">
				  			<form method="post" action="#">
							    	<input type="text" class="textbox" value=" Name" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Name';}">
							    	<input type="text" class="textbox" value="Email" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Email';}">
										<div class="clear"> </div>
								    <div>
								    	<textarea value="Message:" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = ' Message';}">Message</textarea>
								    </div>
								<div class="button send_button">
											   	 <input type="submit" value="提 交">
								</div>
								<div class="clear"> </div>
							</form>
						</div>
						<a class="mov-top" href="#home1"> <span> </span></a>
					 <div class="clear"> </div>
				</div>
  			</div>
     </div>
     <!-- scroll_top_btn -->
		<script type="text/javascript" src="Theme/Default/js/move-top.js"></script>
		<script type="text/javascript" src="Theme/Default/js/easing.js"></script>
	    <script type="text/javascript">
			$(document).ready(function() {
			
				var defaults = {
		  			containerID: 'toTop', // fading element id
					containerHoverID: 'toTopHover', // fading element hover id
					scrollSpeed: 1200,
					easingType: 'linear' 
		 		};
				
				
				$().UItoTop({ easingType: 'easeOutQuart' });
				
			});
		</script>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".scroll").click(function(event){		
				event.preventDefault();
				$('html,body').animate({scrollTop:$(this.hash).offset().top},1200);
			});
		});
	</script>

		 <a href="#" id="toTop" style="display: block;" title="回到顶部"><span id="toTopHover" style="opacity: 1;"></span></a>
<!--------//end-contact----------->
</body>
</html>		