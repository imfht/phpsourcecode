<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">

<link rel="stylesheet" href="__APPURL__/css/mc.css">
<link rel="stylesheet" href="__APPURL__/css/dialog.css">
<link rel="stylesheet" href="__PUBLIC__/css/font-awesome.css">
<script type="text/javascript" src="__PUBLIC__/js/core/jquery.js"></script>
<script type="text/javascript" src="__APPURL__/js/main.js"></script>
<script type="text/javascript" src="__APPURL__/js/dialog_min.js"></script>

<title>会员卡</title>

<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=no,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">

<style>
.list_ul>div:not (:last-of-type ){
	padding-bottom: 0px;
	background: none;
}
div.body{
	padding-top:0px; 
}
.list_ul>div:not(:last-of-type) {
	padding:0px;
	background:none;
	border-bottom: 2px solid #5ac5d4;
}		
.group_btn li a span{
	display: block;
	line-height: 23px;
	text-align: center;
	color: #ffffff;
	font-size: 15px;
}
.group_btn.all li{
	width: 33.33%;
}
.group_btn.only li{
	width: 100%;
}
.group_btn.only li a{
	font-size: 18px;
}
p.page-url{
	max-width: 640px;
	text-align: center;
	border-top: 1px solid rgb(178, 176, 176);
	margin-right: 10px;
	margin-left: 10px;
	margin-top: 17px;
	padding-top: 7px;
}
p.page-url a{
	color:gray;
	font-size:13px;
}
</style>

</head>
<body onselectstart="return true;" ondragstart="return false;">

{include file="$__template_file"}

<style>
footer .nav li.myli>a.on>p, footer .nav li.myli>a.active>p {
	background-position-y: -40px;
}
</style>

<footer>
	<nav class="nav">
		<ul class="box">
			<li><a href="{url('mobile/card')}">
					<p class="card"></p> <span>会员卡</span>
			</a></li>
			<li><a href="{url('mobile/duihuan')}">
					<p class="share"></p> <span>兑换</span>
			</a></li>
			<li class="myli"><a href="{url('mobile/usercenter')}" class="my">
					<p class="my"></p> <span>个人中心</span>
			</a></li>
			<li><a href="{url('mobile/qiandao')}">
					<p class="sign"></p> <span>签到</span>
			</a></li>
			<li><a href="{url('mobile/message')}">
					<p id="Js-msg-num" class="msg"></p> <span>消息<?php //debug(); ?></span>
			</a></li>
		</ul>
	</nav>
</footer>

<script>
$(function(){
	
	var url=location.href;
	if(url.indexOf('/card')!=-1 || url.indexOf('/userinfo')!=-1 || url.indexOf('/cardApplyInfo2')!=-1){
		$('.nav .box li:eq(0) a').addClass('on');
	}else if(url.indexOf('/duihuan')!=-1){
		$('.nav .box li:eq(1) a').addClass('on');
	}else if(url.indexOf('/usercenter')!=-1){
		$('.nav .box li:eq(2) a').addClass('on');
	}else if(url.indexOf('/qiandao')!=-1){
		$('.nav .box li:eq(3) a').addClass('on');
	}else if(url.indexOf('/message')!=-1){
		$('.nav .box li:eq(4) a').addClass('on');
	}
	
})
</script>

</div>
<div mark="stat_code" style="width: 0px; height: 0px; display: none;"></div>
</body>
</html>
