<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>
        后台管理
    </title>
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/lib/Hui-iconfont/1.0.7/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/lib/icheck/icheck.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/css/style.css" />
    <script type="text/javascript" src="/ar/Public/lib/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="/ar/Public/lib/layer/2.1/layer.js"></script>
    <script type="text/javascript" src="/ar/Public/static/h-ui/js/H-ui.js"></script>
    <script type="text/javascript" src="/ar/Public/static/h-ui.admin/js/H-ui.admin.js"></script>
    <script type="text/javascript" src="/ar/Public/laydate/laydate.dev.js"></script>
    <script type="text/javascript" src="/ar/Public/lib/layer/2.1/layer.js"></script>
    
    <script type="text/javascript" src="/ar/Public/lib/laypage/1.2/laypage.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/ar/Public/ichartjs1.2/samples/css/demo.css" />
    <script type="text/javascript" src="/ar/Public/ichartjs1.2/ichart.1.2.min.js"></script> 
    
</head>

<body>
<header class="navbar-wrapper">
	<div class="navbar navbar-fixed-top">
		<div class="container-fluid cl"> <a class="logo navbar-logo f-l mr-10 hidden-xs" href="">后台管理系统</a>   			
			<nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
				<ul class="cl">
					<li class="dropDown dropDown_hover"> <a href="#" class="dropDown_A"><?php echo ($uname); ?> <i class="Hui-iconfont">&#xe6d5;</i></a>
						<ul class="dropDown-menu menu radius box-shadow">
							<li><a href="<?php echo U('Index/logout');?>">退出</a></li>
						</ul>
					</li>
				</ul>
			</nav>
		</div>
	</div>
</header>
<aside class="Hui-aside">
	<div class="menu_dropdown bk_2">
		<?php if(is_array($menuList)): foreach($menuList as $key=>$menu): ?><dl id="">
				<dt><i class="Hui-iconfont">&#xe60d;</i> <?php echo ($menu['name']); ?><i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
				 <dd>
	                <ul>
						<?php if(is_array($menu["son"])): foreach($menu["son"] as $key=>$son): if($son["status"] == 1): ?><li><a _href="<?php echo U($son['controller']."/".$son['action']); ?>" data-title="<?php echo (trim($son["name"])); ?>" href="javascript:;"><?php echo ($son["name"]); ?></a></li><?php endif; endforeach; endif; ?>
	                </ul>
	            </dd>
			</dl><?php endforeach; endif; ?>
	</div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
	<div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
		<div class="Hui-tabNav-wp">
			<ul id="min_title_list" class="acrossTab cl">
				<li class="active"><span title="用户列表" data-href="welcome.html">用户列表</span><em></em></li>
			</ul>
		</div>
		<div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
	</div>
	<div id="iframe_box" class="Hui-article">
		<div class="show_iframe">
			<div style="display:none" class="loading"></div>
			<iframe scrolling="yes" frameborder="0" src="<?php echo U('Admin/index');?>"></iframe>
		</div>
	</div>
</section>
</body>


</html>