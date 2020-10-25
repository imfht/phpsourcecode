<?php

;echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>网站后台管理</title>
<link rel="stylesheet" type="text/css" id="css" href="static/css/admin/main.css">
<script type="text/javascript" src="static/js/jquery.js"></script>
<script type="text/javascript">  
            $(function(){  
                var pagestyle = function() {
					var rframe = $("#report_iframe");
					//ie7默认情况下会有上下滚动条，去掉上下15像素
					var h = $(window).height() - rframe.offset().top - 20;
					rframe.height(h);
				}
				//注册加载事件
				$("#report_iframe").load(pagestyle);
				//注册窗体改变大小事件   
				$(window).resize(pagestyle);
				var $div_li = $("ul.topmenu li");
				$div_li.click(function() {
					$(this).addClass("here").siblings().removeClass("here");
					var index = $div_li.index(this); 
					$("div.menu > ul").eq(index).show().siblings().hide();
				});
				$(".sidebar h3").click(function(){
					//$(this).parent().siblings().children("ul").hide();
					$(this).parent().siblings().children("h3").removeClass("selected");
					var $ul = $(this).next("ul");
					if($ul.is(":visible")){
						$(this).removeClass("selected");
						$ul.hide();
					}else{
						$(this).addClass("selected");
						$ul.show();
					}
					return false;
				 });
				$(".sidebar ul li a").click(function(){
					$(".sidebar ul li a").removeClass("selected");
					$(this).addClass("selected");
				 });
            });  
</script>
</head>
<body>
<div class="top">
	<div class="topbox clearfix">
		<div class="quickmenu">
			<ul class="clearfix">
				<li><a href="';echo WEB_PATH;echo '/" target="_blank">站点首页</a></li>
				<li><a href="';echo ADMIN_PAGE;echo '?m=main">仪表盘</a></li>
				<li><a href="';echo ADMIN_PAGE;echo '?m=cache&a=del">清除缓存</a></li>
				<li><a href="';echo ADMIN_PAGE;echo '?m=login&a=logout">安全退出</a></li>
			</ul>
		</div>
	</div>
</div>
<div class="mainw">
	<div class="main">
		<div class="mainleft">
			<div class="sidebar">
	<h3 class="selected"><span class="title">仪表盘</span></h3>
	<ul class="hide">
		<li><a href="?m=main">首页</a></li>

	</ul>
</div>
            ';include('adminmenu.php');;echo '		</div>
		<div class="mainright">
			<iframe id="report_iframe" frameborder="0" name="report_iframe" src="';echo ADMIN_PAGE;echo '?m=index" width="100%"></iframe>
		</div>
	</div>
</div>
<script language="JavaScript">
	var index_img = new Image;
	function htmlindex(){
		index_img.src = \'';echo ADMIN_PAGE;echo '?m=create_html&a=index\';
	}
	setInterval(htmlindex,300000);
</script>
</body>
</html>
';
?>