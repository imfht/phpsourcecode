<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<title><?php echo $tag['seo.title']; ?></title>
<meta name="keywords" content="<?php echo $tag['seo.keywords']; ?>" />
<meta name="description" content="<?php echo $tag['seo.description'];  ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $tag['path.skin']; ?>css/bootstrap.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo $tag['path.skin']; ?>css/style.css" media="all" />
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin']; ?>js/bootstrap.js"></script>
	
	<!--[if lt IE 9]>
		<link rel="stylesheet" type="text/css" href="<?php echo $tag['path.skin']; ?>css/ie.css" />
	<![endif]-->
	
</head>
<body>
	<div id="header" class="row container">
		<div class="row" id="feedbackRow">
			<div id="feedback"><a href="<?php echo sys_href(5); ?>">意见反馈</a></div>
		</div>
		<div id="headerBar">
			<div id="logo"><a href="<?php echo $tag['path.root']; ?>/">DocCms X</a></div>
				<ul class="unstyled" id="navi">
					<li><a href="<?php echo $tag['path.root']; ?>/">首页</a></li>
                     <?php nav_main() //主导航调用的标签?>
				</ul>
		</div>
	</div>

<div id="pages" class="row container">
	<ul id="sideBar" class="unstyled">
    	<li class="sideBarHover"><?php echo sys_menu_info('title',true) ?></li>
		<?php nav_sub(0,2,1); //侧导航调用的标签?>
	</ul>
	<div id="contents">
		<?php sys_parts(0) //内容调用的标签?>
	</div>

</div>
		<div id="footer">
			<div class="row container">
				<ul id="footerList" class="unstyled">
					<?php nav_main() //主导航调用的标签?>
				</ul>
				<ul id="social" class="unstyled">
					<li id="weibo"><a href="http://weibo.com/doccms" target="_blank">微博</a></li>
					<li id="qq"><a href="http://t.qq.com/doccms" target="_blank">腾讯</a></li>
				</ul>
			</div>
		</div>
		<p id="miibeian"><a href="http://www.doccms.com">Copyright @ 2012 doccms.com Inc. All rights reserved - DocCms X Team  &nbsp;&nbsp;版权所有</a>&nbsp;&nbsp;<a href="http://www.miibeian.gov.cn" target="_blank">豫ICP备12017787号-1</a></p>
	</body>
</html>