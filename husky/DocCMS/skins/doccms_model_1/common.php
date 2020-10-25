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

<body class="page page-id-839 page-parent page-template page-template-services-template-php">	
	<div id="container" class="clearfix">    	
        <div id="top-container-inner">
            <div id="glow-bg-inner">
            	<!-- BEGIN OF MAINMENU -->
                <div id="menu-wrapper">
                    <div id="menu-container">
                        <div id="logo">
                         	<a href="http://www.doccms.com"><img src="<?php echo $tag['path.skin']; ?>images/LogoParteSuperior_V6.png" alt=""/></a>
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
		<div id="header-container-inner">
			<!--..lang_sel_listt-->
			<div id="pagetitle-wrapper" class="wrapper">
				<h1><?php echo sys_menu_info() ?></h1>
				<div id="breadcumb">
					<div class="breadcrumbs"><?php nav_location('>>','首页') //当前位置调用的标签?></div>
            	</div>
			</div><!--#pagetitle-wrapper-->
			<div id="slideshow-shadow"></div>
		</div><!--#header-container-inner-->
	<!-- END OF HEADER -->
	<!-- BEGIN OF CONTENT -->
	<div id="conten">
		<div class="maincontent-inner">
			<?php sys_parts() //内容调用的标签?>
		</div>
<!-- END OF CONTENT -->
<script>
jQuery(document).ready(function(){
	/*iconos*/
	var heights = [];
	jQuery('.col-2').each(function(){
		/*iconos image center*/
		var h3w = jQuery('h3',this).height();
		if(h3w > 26){
			jQuery('h3',this).css('background-position','10px 10px')
		}
		/*paragraph heigh stabilize*/
		var hcol2 = jQuery(this).height();
		heights.push(hcol2);
	});
	var maxHeight = Math.max.apply(Math, heights);
	jQuery('.col-2').height(maxHeight);
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