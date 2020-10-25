<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($sitename); ?> - <?php echo (C("setting.Copyright")); ?> <?php echo (C("setting.Version")); ?> <?php echo (C("setting.Code")); ?></title>
<script language="javascript" type="text/javascript" src="/tuzicms/App/Manage/View/Default/js/jquery.js"></script>
<script src="/tuzicms/App/Manage/View/Default/js/frame.js" language="javascript" type="text/javascript"></script>
<link href="/tuzicms/App/Manage/View/Default/css/frame.css" rel="stylesheet" type="text/css" />
<link href="/tuzicms/App/Manage/View/Default/css/style.css" rel="stylesheet" type="text/css" />
<!--[if IE 6]>
<script src="/tuzicms/App/Manage/View/Default/js/DD_belatedPNG.js" language="javascript" type="text/javascript"></script>
<script>
  DD_belatedPNG.fix('.nav ul li a,.top_link ul li,background');   /* string argument can be any CSS selector */
</script>
<![endif]-->
</head>
<body class="showmenu">
<div class="pagemask"></div>
<div class="head">
<div class="top_logo"></div>
     <div class="nav" id="nav">
      <ul>
		  <li id="menu_count"><a class="thisclass" href="<?php echo U('Admin/right');?>" _for="common" target="main"><b>常规管理</b></a></li>
		  <li id="menu_search"><a href="<?php echo U('System/pcruntime');?>" _for="search" target="main"><b>缓存设置</b></a></li>
		  <li id="menu_order"><a href="<?php echo U('Personal/index');?>" _for="content" target="main"><b>账户管理</b></a></li>
		  <li id="menu_sys"><a href="<?php echo U('System/index');?>" _for="system" target="main"><b>系统设置</b></a></li>
		  <li id="menu_banner"><a href="<?php echo U('Banner/index');?>" _for="banner" target="main"><b>营销管理</b></a></li>
      </ul>
    </div>
	 <div class="top_link">
      <ul>
	    <li id="i_self">你好<?php echo ($_SESSION["admin_name"]); ?> 
		<?php if($v['admin_type']==1): ?><span style="color:#FFFFFF">[普通]</span>
		<?php else: ?>
		<span style="color:#FFFFFF">[超级]</span><?php endif; ?>
		</li>
		<li id="i_hide_menu"><a href="#" id="togglemenu">隐藏菜单</a></li>
        <li id="i_home"><a href="/tuzicms/index.php" target="_blank">首页</a></li> 
        <li id="i_help"><a href="http://www.tuzicms.com/" target="_blank">帮助</a></li>    
        <li id="i_exit"><a href="/tuzicms/index.php/manage/admin/login_out" target="_top">退出</a></li>		
      </ul>
    </div>
</div>
<!-- header end -->
<div class="left">
<div class="span"></div>
<div class="menu" id="menu">
<div id="items_banner">
	<dl id="dl_items_1_1">
		<dt>营销广告管理</dt>
		<dd>
		<ul>
		<li><a href="/tuzicms/index.php/<?php echo ($module); ?>/Banner/index" target="main">栏目广告</a></li>
		</ul>
		</dd>
	</dl>
	<dl id="dl_items_1_1">
		<dt>营销广告管理</dt>
		<dd>
		<ul>
			<?php if(is_array($adlist)): $i = 0; $__LIST__ = $adlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($v["url"]); ?>" target="main"><?php echo ($v["adnav_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
		</dd>
	</dl>
</div>
<div id="items_system">
	<dl id="dl_items_1_1">
		<dt>系统设置</dt>
		<dd>
		<ul>
		<li><a href="<?php echo U('System/index');?>" target="main">网站设置</a></li>
		<li><a href="<?php echo U('System/moban');?>" target="main">模板管理</a></li>
		<li><a href="<?php echo U('kefu/index');?>" target="main">在线客服设置</a></li>
		<li><a href="<?php echo U('Model/index');?>" target="main">模型管理</a></li>
		<li><a href="<?php echo U('Database/index');?>" target="main">数据库管理</a></li>
		<li><a href="<?php echo U('System/fenye');?>" target="main">数据分页</a></li>
		</ul>
		</dd>
	</dl>
</div><!-- Item End -->
<div id="items_content">
<dl id="dl_items_1_2">
        <dt>我的账户</dt>
        <dd>
          <ul>
		<li><a href="<?php echo U('Personal/index');?>" target="main">修改我的信息</a></li>
		<li><a href="<?php echo U('Personal/pass');?>" target="main">修改我的密码</a></li>
		<li><a href="<?php echo U('Personal/listadmin');?>" target="main">后台管理员列表</a></li>
		</ul>
</dd>
</dl>
</div>
<div id="items_search">
<dl id="dl_items_1_3">
	<dt>静态缓存状态</dt>
	<dd>
		<ul>
			<li><a target="main" href="<?php echo U('System/Pcruntime');?>">电脑端缓存</a></li>
			<li><a target="main" href="<?php echo U('System/Mbruntime');?>">手机端缓存</a></li>
		</ul>
	</dd>
</dl>
<dl id="dl_items_1_3">
	<dt>电脑端缓存更新</dt>
	<dd>
		<ul>
			<li><a target="main" href="<?php echo U('System/clearRuntime');?>">清除系统缓存</a></li>
			<li><a target="main" href="<?php echo U('System/clearhtml');?>">一键更新全站</a></li>
			<li><a target="main" href="<?php echo U('System/clearhome');?>">更新首页</a></li>
			<li><a target="main" href="<?php echo U('System/cleargroup');?>">更新栏目</a></li>
			<li><a target="main" href="<?php echo U('System/cleardetail');?>">更新文档</a></li>		
		</ul>
	</dd>
</dl>
<dl id="dl_items_1_3">
	<dt>手机端缓存更新</dt>
	<dd>
		<ul>
			<li><a target="main" href="<?php echo U('System/clearRuntime');?>">清除系统缓存</a></li>
			<li><a target="main" href="<?php echo U('System/mclearhtml');?>">一键更新全站</a></li>
			<li><a target="main" href="<?php echo U('System/mclearhome');?>">更新首页</a></li>
			<li><a target="main" href="<?php echo U('System/mcleargroup');?>">更新栏目</a></li>
			<li><a target="main" href="<?php echo U('System/mcleardetail');?>">更新文档</a></li>		
		</ul>
	</dd>
</dl>
</div>
<div id="items_common">
	<dl id="dl_items_1_4">
		<dt>栏目管理</dt>
		<dd>
		<ul>
			<li><a href="<?php echo U('Category/index');?>" target="main">栏目管理</a></li>
		</ul>
		</dd>
	</dl>
		
	<dl id="dl_items_2_4">
		<dt>内容管理</dt>
		<dd>
		<ul>
			<?php if(is_array($vlist)): $i = 0; $__LIST__ = $vlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($v["url"]); ?>" target="main"><?php echo ($v["column_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			
		</ul>
		</dd>
	</dl>
<dl id="dl_items_3_4">
	<dt>快捷操作</dt>
	<dd>
		<ul>
			<li><a href="<?php echo U('Attribute/index');?>" target="main">主题鉴定</a></li>
			<li><a href="<?php echo U('Link/index');?>" target="main">友情链接</a></li>
			<li><a href="<?php echo U('Guestbook/index');?>" target="main">留言本管理</a></li>		
			<li><a href="<?php echo U('User/index');?>" target="main">会员管理</a></li>
			<li><a href="<?php echo U('Notice/index');?>" target="main">公告管理</a></li>
			<li><a href="<?php echo U('Special/index');?>" target="main">专题管理</a></li>
		</ul>
	</dd>
</dl>
</div>
</div>
</div>
<!-- left end -->
<div class="right">
	<div class="rightContent">
	<iframe id="main" name="main" frameborder="0" src="<?php echo U('Admin/right');?>" ></iframe>
	</div>    
</div>
<!-- right end -->
<div class="qucikmenu" id="qucikmenu">
  <ul>
<li><a href="content_list.htm" target="main">数据列表</a></li>
<li><a href="catalog_main.htm" target="main">栏目管理</a></li>
<li><a href="sys_info.htm" target="main">修改系统参数</a></li>
  </ul>
</div>
<!-- qucikmenu end -->
</body>
</html>