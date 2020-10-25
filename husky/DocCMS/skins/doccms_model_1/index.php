<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html dir="ltr" lang="en-US">
<head>
<title><?php echo $tag['seo.title']; ?></title>
<meta name="keywords" content="<?php echo $tag['seo.keywords']; ?>" />
<meta name="description" content="<?php echo $tag['seo.description'];  ?>" />
<link rel="stylesheet" href="<?php echo $tag['path.skin']; ?>css/style.css" type="text/css" media="screen" />
<!--[if IE 7]>
<link rel="stylesheet" href="<?php echo $tag['path.skin']; ?>css/style-ie7.css" type="text/css" media="screen" />
<![endif]-->
<script type='text/javascript' src='<?php echo $tag['path.skin']; ?>js/jquery.js?ver=1.7.2'></script>
<script type='text/javascript' src='<?php echo $tag['path.skin']; ?>js/functions.js?ver=3.4.1'></script>
</head>
<body class="home blog">	
	<div id="container" class="clearfix">    	
        <div id="top-container">
            <div id="glow-bg">
            	<!-- BEGIN OF MAINMENU -->
                <div id="menu-wrapper">
                    <div id="menu-container">
                        <div id="logo">
                        	<a href="<?php echo $tag['path.root']; ?>/"><img src="<?php echo $tag['path.skin']; ?>images/LogoParteSuperior_V6.png" alt=""/></a>
                        </div>
                        <div id="mainmenu">
                            <ul id="menu" class="">
                            	<li><a href="<?php echo $tag['path.root']; ?>/">首页</a></li>
                                <?php nav_main() //主导航调用的标签?>
                        	</ul>                        
						</div>
                        <div id="login-wrapper">
                              <a class="popup-logout" href="<?php echo sys_href(6); ?>"><span>联系我们</span></a>
                        </div>
                    </div>
                </div>
                <!-- END OF MAINMENU -->
                <!-- END OF HEADER -->
	<!-- BEGIN OF HEADER -->
	<div id="header-container">
		<!--..lang_sel_listt-->
		<div id="slideshow-wrapper" class="wrapper">
        	<div id="slideshow">
            	<ul id="slide-main">
                	<li style="width:960px !important">     
                        <div style="float:left; margin-left: 40px; margin-top: -60px;">
				<a class="video-pop" target="_blank" href="http://www.doccms.com">
                                <img src="<?php echo $tag['path.skin']; ?>images/Pantalla-Mac_4_Noms.png" alt="" /> 
                                <span style="display:none"><iframe width="853" height="480" src="" frameborder="0" allowfullscreen></iframe></span>
                            </a>
                        </div>
	                    <div class="slide-text1">
	                    	<h2>DocCms X 1.0</h2>
							<p>DocCms X —— [ 音译：稻壳Cms ] 是一款将于2012年11月11日正式发布，定位于为企业、站长、开发者、网络公司、VI策划设计公司、SEO推广营销公司、网站初学者等用户 量身打造的一款全新企业建站、内容管理系统，服务于企业品牌信息化建设，也适应用个人、门户网站建设！</p>
	                    </div>
	            	</li>
				</ul>
                <ul id="slide-title">
                	<li class="title " >
                    	<a class="slidelink" href="http://www.doccms.com" target="_blank">
                        	<span style="padding-left:29px; background:url(<?php echo $tag['path.skin']; ?>images/discover.png) no-repeat -2px 9px">DocCms官方网站</span>
                        	</a>
					</li>
                    <li class="title " >
                    	<a class="slidelink" href="http://www.doccms.net" target="_blank">
                        	<span style="padding-left:29px; background:url(<?php echo $tag['path.skin']; ?>images/new-edition.png) no-repeat -2px 9px">DocCms官方论坛</span>
                        </a>
					</li>
                    <li class="title " >
                        <a class="slidelink" href="http://www.doccms.com/AboutUs/" target="_blank">
                           <span style="padding-left:29px; background:url(<?php echo $tag['path.skin']; ?>images/cost-saving.png) no-repeat -2px 9px">关于我们</span>
                        </a>
					</li>
                    <li class="title line-last" >
                        <a class="slidelink" href="http://www.doccms.com/DocCmsX10/"  target="_blank">
                            <span style="padding-left:29px; background:url(<?php echo $tag['path.skin']; ?>images/increase.png) no-repeat -2px 9px">最新版本下载</span>
                        </a>
					</li>
                </ul>
            </div>
        </div>		
		<div id="slideshow-shadow"></div>
	</div><!--#header-container-->
	<!-- BEGIN OF CONTENT -->
	<div id="conten">
		<div id="home"class="maincontent-inner">
        	<h3>新闻资讯</h3>
			<?php doc_list('8|9',4,1,20,100,0,true,false,'id',0)?>
            <div id="piclist">
            	<h3>图片展示</h3>
                <ul>
                    <?php doc_product('15',4,0,10,0,0,true,false,'id',0)?>
                </ul>
            </div>    
        </div><!--.maincontent-->
	</div><!--#content-->
<script>
jQuery(document).ready(function(){
	/*iconos*/
	jQuery('.home-content h4').each(function(){
		var hh = jQuery(this).height();
		if(hh > 22){
			jQuery(this).css('background-position','0px 12px')
		}	
	})
});	
</script>
                <div id="bottom-wrapper">        
                    <div id="bottom-content">
                        <div class="logofooter-column">
                        	<img src="<?php echo $tag['path.skin']; ?>images/LogoParteSuperior_V6.png" alt="" class="footerlogo"/>
                        </div>
                    	<?php nav_main(1) //主导航调用的标签?>						
					</div><!--#bottom-content-->
					<div id="slideshow-shadow" class="lastt"></div>
                </div><!--#bottom-wrapper-->           
                
                <div id="copyright-text" class="clearfix">
					<div class="center clearfix" style="margin:0 auto; widht:960px">
						<div class="back-top">
	                  		<a href="#top" class="scroll">back to top</a>&nbsp;<a href="#top" class="scroll backtop"><img src="<?php echo $tag['path.skin']; ?>images/back-top.png" alt="" /></a>
						</div><!--.back-top-->
					</div><!--.center-->
						<p style="text-align:center; margin:20px 0 0 0">© 2012 Power by <a title="稻壳Cms官方网站" href="http://www.doccms.com">DocCms X.Team</a>,<a title="稻壳网" href="http://www.doooc.com">DoooC.com</a>. Address: China Henan .- Zhengzhou   /     <a title="稻壳Cms官方论坛" href="http://www.doccms.net">Terms of Service</a> - <a title="法律声明" href="http://www.doccms.com/PurchaseAgreement/">Privacy Policy</a><br><spam></spam></p>
                </div><!--.copyright-text-->
			</div><!--#glow-bg-->
		 </div><!--#top-container-->
    </div><!--#container-->      
        </body>
</html>