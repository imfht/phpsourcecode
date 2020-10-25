<!DOCTYPE html>
<html lang="en">
<head>
<title>物资管家</title>
<!-- custom-theme -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //custom-theme -->
<link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"
	media="all" />
<link href="{{asset('css/indexStyle.css')}}" rel="stylesheet" type="text/css" media="all" />
<!-- js -->
<script type="text/javascript" src="{{asset('js/jquery.min.js')}}"></script>
<!-- //js -->
<link rel="stylesheet" href="{{asset('css/flexslider.css')}}" type="text/css"
	media="screen" property="" />
<!-- font-awesome-icons -->
<link href="{{asset('css/font-awesome.css')}}" rel="stylesheet">
<!-- //font-awesome-icons -->
<link href="{{asset('css/googleleapis-latinext-vietnames.css')}}" rel="stylesheet">
<link href="{{asset('css/googleleapis-cyrillic-greek.css')}}" rel="stylesheet">
</head>

<body>
	<!-- banner -->
	<div class="banner">
		<div class="container">
			<nav class="navbar navbar-default">
				<div class="navbar-header navbar-left">
					<button type="button" class="navbar-toggle collapsed"
						data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span> <span
							class="icon-bar"></span> <span class="icon-bar"></span> <span
							class="icon-bar"></span>
					</button>
					<h1>
						<a class="navbar-brand" href="#"><span>M</span><i>物资管家</i></a>
					</h1>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse navbar-right"
					id="bs-example-navbar-collapse-1">
					<nav class="link-effect-2" id="link-effect-2">
						<ul class="nav navbar-nav">
							<li class="active"><a href="index.html" class="effect-3">主页</a></li>
							<li><a href="#service" class="effect-3">服务</a></li>
							<li><a href="#about" class="effect-3">关于</a></li>
						@if (Route::has('login'))
                    		@if (Auth::check())
                        	<li><a href="{{ url('/home') }}" class="effect-3">管理中心</a></li>
                    		@else
                        	<li><a href="{{ url('/login') }}" class="effect-3">登陆</a></li>
                        	<li><a href="{{ url('/register') }}" class="effect-3">注册</a></li>
                   			 @endif
            			@endif
						</ul>
					</nav>
				</div>
			</nav>
			<div class="w3_agile_banner_info">
				<h3>
					方便<span> </span>可靠
				</h3>
				<h2>简化企业物资管理的小助手</h2>
				<ul>

				</ul>
			</div>
			<div class="agile_arrow_bounce">
				<a href="#about" class="agile_arrow bounce scroll"></a>
			</div>
		</div>
	</div>
	<!-- //banner -->
	<!-- strip -->
	<div class="agileits_w3layouts_strip">
		<div class="container">
			<div class="w3_agileits_strip_left">
				<h3>任何地方，随时随刻管理您的企业物资</h3>
			</div>
			<div class="w3_agileits_strip_right">
				<a href="/login">开始使用</a>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<!-- //strip -->
	<!-- banner-bottom -->
	<div class="banner-bottom">
		<div class="container">
			<div class="agileits_heading_section">
				<img src="/img/index/2.png" alt=" " class="img-responsive" />
				<h3 class="wthree_head">物资管家的优势</h3>
			</div>
			<div class="w3ls_banner_bottom_grids">
				<div class="col-md-4 w3ls_banner_bottom_grid">
					<div class="w3l_banner_bottom_grid1">
						<i class="fa fa-wrench hvr-pulse-shrink" aria-hidden="true"></i>
					</div>
					<h4>灵活 可靠</h4>
					<p>管理员可轻松的为公司组织结构进行建模，可以无缝的适应规模快速扩张的成长型企业，长期适用，数据有保障</p>
				</div>
				<div class="col-md-4 w3ls_banner_bottom_grid">
					<div class="w3l_banner_bottom_grid1">
						<i class="fa fa-desktop hvr-pulse-shrink" aria-hidden="true"></i>
					</div>
					<h4>漂亮 实用</h4>
					<p>响应式的设计，使得您可以在任何联网的各种设备上24小时不间断的操作</p>

				</div>
				<div class="col-md-4 w3ls_banner_bottom_grid">
					<div class="w3l_banner_bottom_grid1">
						<i class="fa fa-users hvr-pulse-shrink" aria-hidden="true"></i>
					</div>
					<h4>简单 方便</h4>
					<p>通用的几步简单的点击，即可轻松的完成物资管理的各个业务流程</p>
				</div>

				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<!-- //banner-bottom -->
	
	<!-- quotes -->
	<div class="quotes" id="service">
		<div class="container">
			<h3>
				不断追求<span>高效、实用</span>的信息系统
			</h3>
		</div>
	</div>
	<!-- //quotes -->
	<!-- events -->
	<div class="events">
		<ul id="flexiselDemo1">
			<li>
				<div class="w3layouts_event_grid">
					<div class="w3_agile_event_grid1">
						<img src="/img/index/1.jpg" alt=" " class="img-responsive" />
						<div class="w3_agile_event_grid1_pos">
							<p>申请购买</p>
						</div>

					</div>
					<div class="agileits_w3layouts_event_grid1">
						<h3>
							<a href="#" data-toggle="modal" data-target="#myModal">购买流程</a>
						</h3>
						<p>1 当发现没有想要的物资时，可以申请购买</p>
						<p>2 管理员审批。若是同意，则购买后分派物资</p>
						<p>3 申请者确认收到申请的物资</p>
					</div>
				</div>
			</li>
			<li>
				<div class="w3layouts_event_grid">
					<div class="w3_agile_event_grid1">
						<img src="/img/index/8.jpg" alt=" " class="img-responsive" />
						<div class="w3_agile_event_grid1_pos">
							<p>信息管理</p>
						</div>

					</div>
					<div class="agileits_w3layouts_event_grid1">
						<h3>
							<a href="#" data-toggle="modal" data-target="#myModal">完善的基本信息管理</a>
						</h3>
						<p>1 基于树形结构的增删改查，清晰明了</p>
						<p>2 多个维度的模糊查询物资信息</p>
					</div>
				</div>
			</li>
			<li>
				<div class="w3layouts_event_grid">
					<div class="w3_agile_event_grid1">
						<img src="/img/index/7.jpg" alt=" " class="img-responsive" />
						<div class="w3_agile_event_grid1_pos">
							<p>租借</p>
						</div>

					</div>
					<div class="agileits_w3layouts_event_grid1">
						<h3>
							<a href="#" data-toggle="modal" data-target="#myModal">物资租借</a>
						</h3>
						<p>1 提供送货上门服务，方便用户的使用</p>
						<p>2 短信通知，方便高效</p>
					</div>
				</div>
			</li>
			<li>
				<div class="w3layouts_event_grid">
					<div class="w3_agile_event_grid1">
						<img src="/img/index/9.jpg" alt=" " class="img-responsive" />
						<div class="w3_agile_event_grid1_pos">
							<p>统计</p>
						</div>

					</div>
					<div class="agileits_w3layouts_event_grid1">
						<h3>
							<a href="#" data-toggle="modal" data-target="#myModal">统计报表</a>
						</h3>
						<p>1 快速统计总体信息</p>
						<p>2 提供最频繁使用和预约最多次数的统计等，为物资购买决策提供有力支持</p>
					</div>
				</div>
			</li>
		</ul>
	</div>
	<!-- //events -->
	<!-- about -->
	<div id="about" class="banner-bottom">
		<div class="container">
			<div class="agileits_heading_section">
				<img src="/img/index/2.png" alt=" " class="img-responsive" />
				<h3 class="wthree_head">关于物资管家</h3>
			</div>
			<div class="w3ls_banner_bottom_grids">
				<div class="col-md-6 w3_agileits_about_grid_left">
					<p>
						企业单位通常需要管理大量的设备，建立物资管理系统可以有效地节约人力物力资源，并提高管理效率。一个企业物资管理系统应具有以下功能：</p>
					<ul>
						<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>1.实现物资的购入、登记、报废等管理</li>
						<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>2.可将各类物资分配到企业各个科室以便使用</li>
						<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>3.可按照物资类别，名称，价格、科室等查询、统计</li>
						<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>4.可生成相应的统计报表</li>
						<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>5.个人申报功能；其他说明、限制：所管理的物资分两大类：固定资产（如家具、电器）、耗材（文具等）；每一件固定资产有唯一的资产编号；物资管理员可以完成以上1、2、3、4功能，而普通员工只可查询本人、本科室相关的情况</li>
					</ul>
				</div>
				<div class="col-md-6 w3_agileits_about_grid_right">
					<iframe src=""></iframe>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<!-- //about -->
	<!-- footer -->
	<div class="footer">
		<div class="container">
			<div class="w3_agile_footer_grids">
				<div class="col-md-4 w3_agile_footer_grid">
					<h3>最新消息</h3>
					<ul class="agile_footer_grid_list">
						<li><i class="fa fa-twitter" aria-hidden="true"></i>增加短信校验通知功能 <span>2017年4月10日</span></li>
						<li><i class="fa fa-twitter" aria-hidden="true"></i>完成最近预约次数最多的物资的图表统计功能
							<span>2017年4月5日</span></li>
					</ul>
				</div>
				<div class="col-md-4 w3_agile_footer_grid">
					<h3>导航</h3>
					<ul class="agileits_w3layouts_footer_grid_list">
						<li><i class="fa fa-angle-double-right" aria-hidden="true"></i><a
							href="#">主页</a></li>
						<li><i class="fa fa-angle-double-right" aria-hidden="true"></i><a
							href="#about">服务</a></li>
						<li><i class="fa fa-angle-double-right" aria-hidden="true"></i><a
							href="/login">登陆/注册</a></li>
					</ul>
				</div>
				<div class="col-md-4 w3_agile_footer_grid">
					<h3>相册</h3>
					<div class="w3_agileits_footer_grid_left">
						<a href="#" data-toggle="modal" data-target="#myModal"> <img
							src="/img/index/7.jpg" alt=" " class="img-responsive" />
						</a>
					</div>
					<div class="w3_agileits_footer_grid_left">
						<a href="#" data-toggle="modal" data-target="#myModal"> <img
							src="/img/index/8.jpg" alt=" " class="img-responsive" />
						</a>
					</div>
					<div class="w3_agileits_footer_grid_left">
						<a href="#" data-toggle="modal" data-target="#myModal"> <img
							src="/img/index/9.jpg" alt=" " class="img-responsive" />
						</a>
					</div>
					<div class="w3_agileits_footer_grid_left">
						<a href="#" data-toggle="modal" data-target="#myModal"> <img
							src="/img/index/10.jpg" alt=" " class="img-responsive" />
						</a>
					</div>
					<div class="w3_agileits_footer_grid_left">
						<a href="#" data-toggle="modal" data-target="#myModal"> <img
							src="/img/index/14.jpg" alt=" " class="img-responsive" />
						</a>
					</div>
					<div class="w3_agileits_footer_grid_left">
						<a href="#" data-toggle="modal" data-target="#myModal"> <img
							src="/img/index/15.jpg" alt=" " class="img-responsive" />
						</a>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="w3ls_address_mail_footer_grids">
				<div class="col-md-4 w3ls_footer_grid_left">
					<div class="wthree_footer_grid_left">
						<i class="fa fa-map-marker" aria-hidden="true"></i>
					</div>
					<p>
						陕西省西安市 <span>西安电子科技大学</span>
					</p>
				</div>
				<div class="col-md-4 w3ls_footer_grid_left">
					<div class="wthree_footer_grid_left">
						<i class="fa fa-phone" aria-hidden="true"></i>
					</div>
					<p>+(86) 173 7811 8015</p>
				</div>
				<div class="col-md-4 w3ls_footer_grid_left">
					<div class="wthree_footer_grid_left">
						<i class="fa fa-envelope-o" aria-hidden="true"></i>
					</div>
					<p>
						<a href="mailto:info@example.com">1373918920@qq.com</a> <span><a
							href="mailto:info@example.com">uchiyou@sina.com</a></span>
					</p>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="agileinfo_copyright">
				<p>版权所有 &copy; 2017.zhouyou.</p>
			</div>
		</div>
	</div>
	<!-- //footer -->
	<!-- flexSlider -->
	<script defer src="{{asset('js/jquery.flexslider.js')}}"></script>
	<script type="text/javascript">
$(window).load(function(){
	$('.flexslider').flexslider({
		animation: "slide",
		start: function(slider){
			$('body').removeClass('loading');
		}
	});
});
	</script>
	<!-- //flexSlider -->
	<!-- flexisel -->
	<script type="text/javascript" src="{{asset('js/jquery.flexisel.js')}}"></script>
	<script type="text/javascript">
	$(window).load(function() {
		$("#flexiselDemo1").flexisel({
			visibleItems: 4,
			animationSpeed: 1000,
			autoPlay: true,
			autoPlaySpeed: 3000,
			pauseOnHover: true,
			enableResponsiveBreakpoints: true,
			responsiveBreakpoints: {
				portrait: {
					changePoint:480,
					visibleItems: 1
				},
				landscape: {
					changePoint:640,
					visibleItems:2
				},
				tablet: {
					changePoint:768,
					visibleItems: 3
				}
			}
		});
				
	});
		</script>
	<!-- //flexisel -->
	<!-- for bootstrap working -->
	<script src="{{asset('js/bootstrap.min.js')}}"></script>
	<!-- //for bootstrap working -->
	<!-- start-smooth-scrolling -->
	<script type="text/javascript" src="{{asset('js/move-top.js')}}"></script>
	<script type="text/javascript" src="{{asset('js/easing.js')}}"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".scroll").click(function(event){
				event.preventDefault();
				$('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
			});
		});
			</script>
	<!-- start-smooth-scrolling -->
	<!-- here stars scrolling icon -->
	<script type="text/javascript">
			$(document).ready(function() {
				/*
				 var defaults = {
				 containerID: 'toTop', // fading element id
				 containerHoverID: 'toTopHover', // fading element hover id
				 scrollSpeed: 1200,
				 easingType: 'linear'
				 };
				 */

				$().UItoTop({ easingType: 'easeOutQuart' });

			});
				</script>
	<!-- //here ends scrolling icon -->
</body>
</html>