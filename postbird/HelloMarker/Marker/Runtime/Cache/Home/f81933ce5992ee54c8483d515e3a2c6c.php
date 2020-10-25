<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="hellomarker,postbird,李瞻文,ptbird">
    <meta name="description" content="hellomarker,一本正经的吃喝玩乐！Powered by postbird!">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>HelloMarker | 一本正经地吃喝玩乐</title>
    <link rel="stylesheet" href="/hellomarkertest/Public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/marker.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/jquery-clock.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/weatherIcon.css">
    <script src="/hellomarkertest/Public/js/jquery-1.10.2.js"></script>
    <script src="/hellomarkertest/Public/js/bootstrap.min.js"></script>
    <script>
      var appUrl="/hellomarkertest/marker.php";
    </script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/marker.js"></script>
    <script src="/hellomarkertest/Public/js/kindeditor/kindeditor-min.js"></script>
</head>
<body style="background-color:#F4F8FA;letter-spacing:0.2px;">
    <div style="background-color:#fff;">
        <div class="container">
              <div class="log-nav">
              <div style="float:left;">
                   <a href="/hellomarkertest" class="navbar-band logo-a"></a>
                   <span class="slogan visible-lg"> | 一 本 正 经 地 吃 喝 玩 乐 </span>
              </div>
                <ul class="nav navbar-right">
                 <?php if($userLoginFlag == 0): ?><li ><a href="/hellomarkertest/marker.php/Home/User/index">登录</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/User/userSign">注册</a></li>
                <?php else: ?>
                    <li title='我的Marker'><a href="/hellomarkertest/marker.php/Home/Index/myNote/"><i class='fa fa-bookmark-o' ></i></a></li>
                    <li title='我的应用' class="visible-lg"><a href="/hellomarkertest/marker.php/Home/Work/myWork/"><i class='fa fa-cubes' ></i></a></li>
                    <li class="visible-lg dropdown user-header ">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:"><i class='fa fa-user-secret'></i> <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/hellomarkertest/marker.php/Home/Index/myHome/user/<?php echo ($usersessionname); ?>">我的主页</a></li>
                            <li><a href="/hellomarkertest/marker.php/Home/User/myChange/">编辑个人资料</a></li>
                            <li class="divider"></li>
                            <li><a href="/hellomarkertest/marker.php/Home/User/userLogout">退出</a></li>
                        </ul>
                    </li>
                    <li title='主页' class="hidden-lg"><a href="/hellomarkertest/marker.php/Home/Index/myHome/user/<?php echo ($usersessionname); ?>"><i class='fa fa-user-secret' ></i></a></li>
                    <li title='退出' class="hidden-lg"><a href="/hellomarkertest/marker.php/Home/User/userLogout"><i class='fa fa-power-off' ></i></a></li><?php endif; ?>
               </ul> 
               </div>
        </div>
    </div>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header ">
              <button type="button" class="navbar-toggle collapsed " data-toggle="collapse" data-target="#nav-header" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand visible-xs" href="#">专 注 生 活 </a>
            </div>
            <div class="collapse navbar-collapse" id="nav-header">
                <ul class="nav navbar-nav ">
                    <li ><a href="/hellomarkertest/marker.php/Home/Index/">首页</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Article/">吾说八道</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/Index/markerShare/sid/all">吃喝玩乐</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Work/">一「本」正经</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/Account/">柴米油盐</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Weather/">未雨绸缪</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/App/">也有APP</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-ul">
                <li><a href="/hellomarkertest/marker.php/Home/Bug/"><i class="fa fa-map-o"></i>&nbsp;&nbsp; 「 略懂七八 」</a></li>
                </ul>
            </div>
        </div>
    </nav>

<link rel="stylesheet" href="/hellomarkertest/Public/css/app-style.css">
<link rel="stylesheet" href="/hellomarkertest/Public/css/app-owl.carousel.css">
<script src="/hellomarkertest/Public/js/app-owl.carousel.js"></script>


<div style=";background-image: url('/hellomarkertest/Public/images/app/banner.jpg');margin-top:-20px;">
	<div class="container">
	<div class="row">
    <div class="col-md-6">
		<div id="owl-demo1" class="owl-carousel" style="margin-top:15%;width:101%;">
			<div class="item-bottom">
	            <div class="item-right">
					<h1>HelloMarker</h1>
					<span>移动客户端，满足你的小需求！</span>
					<p>hellomarker，多平台共通使用，满足用户对于移动端的需求。 </p>
					<p>采用目前流行的html5 WEBAPP开发，使用wex5框架，最小化APP至个位数MB，不占用过多空间，且创新性的采用HTML5缓存基础，HTML5 memcache技术，最大程度的接近native APP，给用户带来流畅体验。</p>
					<p>简洁设计，轻松使用，拒绝繁琐！ </p>
					<a href="/hellomarkertest/Public/app/hellomarker.apk"><i class="fa fa-download pull-left"></i><i class="fa fa-android"></i>  Android  </a>
					<a href="/hellomarkertest/Public/app/hellomarker.ipa"><i class="fa fa-download pull-left"></i><i class="fa fa-apple"></i>  IOS  </a>
				</div>
			</div>
			<div class="item-bottom">
	            <div class="item-right">
					<h1><i class="fa fa-android"></i> Android</h1>
					<span>轻松生活，一手掌握！</span>
					<p>hellomarker Android版本系统需求安卓5.0以上为最佳，兼容安卓4.4X版本</p>
					<p>安卓版本下载请直接点击下载后使用腾讯管家或其他APK桌面安装软件进行安装，目前对于大多数安卓手机可有效兼容，目前该APP处于测试阶段，当前测试版本为1.1.1，包：com.ptbird.hellomarker.</p>
					<p>方便快捷，更多消息，一手掌握！</p>
					<a href="/hellomarkertest/Public/app/hellomarker.apk" style="background-color: #CC3333;"><i class="fa fa-download pull-left"></i><i class="fa fa-android"></i>  Android  </a>
				</div>
			</div>
		    <div class="item-bottom">
	            <div class="item-right">
					<h1><i class="fa fa-apple"></i> Apple IOS</h1>
					<span>未雨绸缪，一本正经！</span>
					<p>hellomarker IOS版本目前处于内部测试阶段，开发者postbird采用MAC OSX CODEX7测试P12证书</p>
					<p>目前无法在APPLE STORE中下载，未进行发布，因此安装只能通过Itunes或PP助手进行安装,下载下后为IPA包，为真机测试包，com.ptbird.hellomarker。（需要通过数据线连接手机和电脑）</p>
					<p>安装方法详细见下链接。</p>
					<a href="/hellomarkertest/Public/app/hellomarker.ipa" style="background-color: #9900FF;"><i class="fa fa-download pull-left"></i><i class="fa fa-apple"></i>  IOS  </a>
				</div>
			</div>						
		</div>
        <script>
		    $(document).ready(function() {
		      $("#owl-demo1").owlCarousel({
		        items : 1,
		        lazyLoad : true,
		        autoPlay : true,
		        navigation : true,
		        navigationText :  true,
		        pagination : false,
		      });
		    });
		  </script>
    </div>
    <div class="col-md-6 banner-side" >
		<div class="col-md-6 side"style="margin-top:15%;">
			<img class="img-responsive" src="/hellomarkertest/Public/images/app/ba.png" alt="hellomarker">
		</div>
		<div class="col-md-6 side">
			<img class="img-responsive" src="/hellomarkertest/Public/images/app/ba1.png" alt="hellomarker">
		</div>
		<div class="clearfix"> </div>
	</div>

  </div>
</div>
</div>
	<div class="content">
		<div class="container">
			<div class="content-top">
				<div class="col-md-4 grid">
					<h3><i></i>微信公众号</h3>
					<p>微信扫一扫关注hellomarker公众号,为微信用户提供便捷服务</p>
					<img src="/hellomarkertest/Public/uploads/wechat.jpg" alt="hellomarker"width="250px;"height="250px;">
				</div>
				<div class="col-md-4 grid">
					<h3><i class="mid"></i>安卓客户端</h3>
					<p>由于微信自动屏蔽APK文件，请使用手机自带扫一扫或默认浏览器扫一扫功能。</p>
					<img src="/hellomarkertest/Public/uploads/apk.png" alt="hellomarker" width="250px;"height="250px;">
				</div>
				<div class="col-md-4 grid">
					<h3><i class="just"></i> IOS客户端</h3>
					<p>请使用safari浏览器打开文件，如无法安装，请使用PP助手安装。</p>
					<img src="/hellomarkertest/Public/uploads/ios.png" alt="hellomarker" width="250px;"height="250px;">
				</div>
				<div class="clearfix"> </div>
			</div>
			<div class="content-grid">
				<h2>IOS客户端如何使用PP助手或Itunes进行APP的安装</h2>
				<p class="text-left"><a href="http://221.181.104.59/cache/ghost.25pp.com/soft/pppc_setup/pp2.0_ad_ppwin_Setup.exe?ich_args=173c253bc0c59c5972e986567aeb138c_7855_0_0_5_0e6b8e54057018a9775f39243ab40c08c55306f263f55217141f095e2a4fe056_5322a95c39efe99733a901e4d544fe3f_1_0&ich_ip=" target="_blank" ><font style="font-size:20px;">【软件】PP助手（点击链接直接下载 PP助手2.0版）</font> </a></p>
				<p class="text-left"><a href="http://www.25pp.com/news/news_2647.html" target="_blank" ><font style="font-size:20px;">【教程】如何使用PP助手给未越狱的iOS设备安装应用程序</font> </a></p>
				<p class="text-left"><a href="http://www.25pp.com/news/news_2407.html" target="_blank" ><font style="font-size:20px;">【教程】关于使用PP助手安装免越狱版软件出现闪退的解决方法</font> </a></p>
				<p class="text-left"><a href="http://www.25pp.com/news/news_2397.html" target="_blank" ><font style="font-size:20px;">【教程】关于使用PP助手安装软件提示失败的解决办法</font> </a></p>
				<p class="text-left"><a href="http://www.xpgod.com/article/2110.html" target="_blank" ><font style="font-size:20px;">【教程】使用Itunes给iPhone iPad安装ipa文件</font> </a></p>
			</div>
		</div>
		<div class="content-bottom">
			<div class="container">
				<h3>一本正经地问：你准备好了吗？</h3>
				<p>THERE I AM ，YOUR LIFE IS MORE BEAUTIFUL ! </p>
			</div>
			<i> </i>
		</div>

<div class="container">
	<footer>
       <p class="pull-right "><a href="#" class="scrollToTop">Back to top</a></p>
    </footer>
</div>
<footer class="footer"style="margin-top:20px">

		<div class=" text-center ">
	        <h3><small>powered by <a href="http://www.ptbird.cn" target="_blank">postbird</a></small></h3>
        </div>
</footer>
</body>
</html>