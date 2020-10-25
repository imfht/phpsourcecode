<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PJY后台管理</title>

<script language="JavaScript" src="<?php echo base_url()?>Public/admin_style/js/jquery.min.js"></script>
<script type="text/javascript">
$(function(){
	$(".menuTitle").next("div").slideUp();
	$(".menuTitle").click(function(){
		$(this).next("div").slideToggle("normal").siblings(".menuContent:visible").slideUp("normal");
		$(this).attr("class",$(this).attr("class")=="menuTitle"?"activeTitle":"menuTitle");
		$(this).siblings(".activeTitle").attr("class","menuTitle");
	});
	$(".endTitle").click(function(){
		$(this).next("div").slideToggle("normal").siblings(".menuContent:visible").slideUp("normal");
		$(this).siblings(".activeTitle").attr("class","menuTitle");
	});
});

</script>
<style type="text/css">
<!--
body {
	margin: 0;
	width:100%;
	font-family: Arial, Verdana;
	font-size: 9pt;
	color: #111;
	scrollbar-face-color: #E7F5FE; 
	scrollbar-highlight-color: #006699; 
	scrollbar-shadow-color: #006699; 
	scrollbar-3dlight-color: #E7F5FE; 
	scrollbar-arrow-color: #006699; 
	scrollbar-track-color: #E7F5FE; 
	scrollbar-darkshadow-color: #E7F5FE; 
	scrollbar-base-color: #E7F5FE;
}
a {text-decoration: none; color: #111;}
a:hover {color: red;}
.tree {white-space: nowrap; margin:3px 0 3px 10px;}
.tree img {border:0px;padding:0px;margin:0px;vertical-align: middle;}
.tree .folder {cursor:pointer;} 
.tree a {color: #000;text-decoration: none; outline:none;/*outline:0;*/}
.tree a { outline:none; }
.tree a.node, .tree a.nodeSel {white-space: nowrap;padding: 1px 2px 1px 2px;}
.tree a.node:hover, .tree a.nodeSel:hover {color: red;text-decoration: underline;}
.tree a.nodeSel {background-color: #c0d2ec;}
.leftmenu{width:147px;}
.menuTitle, .activeTitle, .endTitle{width:100%; height:23px; text-align:center; line-height:23px; font-size:12px; font-weight:bold; cursor:pointer;}
.menuTitle{background-image:url(<?php echo base_url()?>Public/admin_style/images/main_34_1.gif);}
.activeTitle, .endTitle{background-image:url(<?php echo base_url()?>Public/admin_style/images/main_34.gif);}
.menuContent{background-color:#fff; margin:0;width:100%;text-align:left;}
.menuContent li{background:url(<?php echo base_url()?>Public/admin_style/images/arr.gif) no-repeat 35px 6px ; list-style-type:none;padding:0px 0px 0px 48px; font-size:12.7px; height:20px; line-height:20px;}
.menuContent ul{margin:0;padding:0;}
.bottom{width:100%;height:23px;background-image:url(<?php echo base_url()?>Public/admin_style/images/main_34.gif);}
-->
</style>
</head>
<body>
<div class="leftmenu">
	<div class="menuTitle">基本设置</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/right')?>" target="right">系统信息</a></li>
			<li><a href="<?php echo site_url('admin/setInfo')?>" target="right">基本设置</a></li>
		</ul>
	</div>	
	<div class="menuTitle">用户管理</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/userinfo')?>" target="right">用户管理</a></li>
		</ul>
	</div>
	<div class="menuTitle">活动管理</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/actadd')?>" target="right">添加活动</a></li>
            <li><a href="<?php echo site_url('admin/actindex')?>" target="right">管理活动</a></li>
		</ul>
    </div>	
	<div class="menuTitle">众筹管理</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/zcAdd')?>" target="right">添加众筹</a></li>
            <li><a href="<?php echo site_url('admin/zcIndex')?>" target="right">管理众筹</a></li>
		</ul>
    </div>	
	<div class="menuTitle">捐书管理</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/bookadd')?>" target="right">添加书籍</a></li>
            <li><a href="<?php echo site_url('admin/bookindex')?>" target="right">管理书籍</a></li>
		</ul>
    </div>	
	<div class="menuTitle">保险基金</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/safeadd')?>" target="right">添加文章</a></li>
            <li><a href="<?php echo site_url('admin/safeindex')?>" target="right">管理文章</a></li>
		</ul>
    </div>	
	<div class="menuTitle">支教有感</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/youganadd')?>" target="right">添加文章</a></li>
            <li><a href="<?php echo site_url('admin/youganindex')?>" target="right">管理文章</a></li>
		</ul>
    </div>	
	<div class="menuTitle">爱心名人</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/heartAdd')?>" target="right">添加人员</a></li>
            <li><a href="<?php echo site_url('admin/heartindex')?>" target="right">管理人员</a></li>
		</ul>
    </div>
	<div class="menuTitle">关于我们</div>
	<div class="menuContent">
		<ul>
			<li><a href="<?php echo site_url('admin/about')?>" target="right">修改内容</a></li>
		</ul>
    </div>

</div>
</body>
</html>
