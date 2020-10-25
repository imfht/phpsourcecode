<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
<title>{$title}</title>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/admin/css/frame.css" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/font-awesome.css">
<script type="text/javascript" src="__PUBLIC__/js/core/jquery.js"></script>
<script type="text/javascript" src="__PUBLIC__/admin/js/jquery.layout.js"></script>
<script type="text/javascript">
var myLayout;
$(document).ready(function(){
myLayout=$("body").layout({west__minSize:40,spacing_open:4,spacing_closed:4,east__initClosed:true,north__spacing_open:0,south__spacing_open:0,togglerLength_open:30,togglerLength_closed:60});
});
</script> 
<style type="text/css">
    .menuitem
        {background: #368ddd no-repeat 5px 5px; position: relative; float: left; text-align: left; line-height: 28px; padding-left: 10px; padding-right: 10px;  color: #fff; margin-left: 8px; font-size: 12px; font-weight: bold;}
    /** 下面的控制显示和隐藏 **/
    .menuitem .submenu
        {display: none;}
    .menuitem:hover .submenu
        {display: block;}
</style>
</head>
<body style="MARGIN: 0px" scroll="no">
<div class="ui-layout-north" onmouseover="myLayout.allowOverflow(this)" onmouseout="myLayout.resetOverflow(this)">
	<div class="header">
    <div class="logo">{$title}</div>
    <div class="right_menu">
	  <span class="menuitem">
        {if empty($ppid) }
		<div><i class="fa fa-list"></i> 选择公众账号</div>
		<div class="submenu">
            {loop $pplist $vo}
			<div onclick="changeppacount({$vo['id']});">{$vo['name']}</div>
            {/loop}
		</div>
		{else}
		<div><i class="fa fa-list"></i> 当前账号：{$ppinfo['name']}</div>
		<div class="submenu">
            {loop $pplist $vo}
			<div onclick="changeppacount({$vo['id']});">{$vo['name']}</div>
            {/loop}
			<div onclick="changeppacount('ppout');">退出此账号</div>
		</div>
		{/if}
      </span>
	  <SPAN><A href="{url('index/password')}" target="main"><i class="fa fa-lock"></i> 修改密码</A></SPAN>
	  <span><a href="{url('index/welcome')}" target="main"><i class="fa fa-tachometer"></i> 后台主页</a></span>
	  <span><a href="{url('index/clearCache')}" target="main"><i class="fa fa-refresh"></i> 更新缓存</a></span>
	  <span><a onclick="return confirm('确定注销登录吗？')" href="{url('index/logout')}" title="点击可以注销登录" ><i class="fa fa-user"></i> 注销用户：{$userinfo['username']}</a></span>
	</div>
  </div>
</div>
<div class="ui-layout-west">
	<div id="menu">
	{loop $leftMenu $menu}
		<div class="menubg_1 cursor" url="{$menu['url']}">{$menu['title']}</div>
		{if !empty($menu['list'])}
		<ul class="none">
		  {loop $menu['list'] $name $url}
		  <li><a href="{$url}" target="main">{$name}</a> </li>
		  {/loop}
		</ul>
		{/if}
	{/loop}
	</div>
</div>
<div class="ui-layout-center">
  <iframe style="OVERFLOW: visible" id="main" height="100%" src="{$iframeUrl}" frameborder="0" width="100%" name="main" scrolling="Yes"> </iframe>
</div>
<script type="text/javascript"> 
$(function(){
	$("#menu").find('DIV').first().attr('class','menubg_2');
	$("#menu").find('UL').first().show();
	$("#menu DIV").click(function(){
		$("#menu DIV").attr('class','menubg_1');
		$("#menu UL").hide();
		$(this).attr('class','menubg_2');
		$(this).next().show();
		var url = $(this).attr('url');
		if(url){ $("#main").attr('src',url); }
	});
});

function changeppacount(id){
	if( id == "ppout" ){
		window.location.href = "{url('index/pplogout')}";
	}else{
		var url = "{url('ppacount/index/ppacountselect')}";
		window.location.href = url +"&id="+id;
	}	
}
</script>
</div>
</body>
</html>
