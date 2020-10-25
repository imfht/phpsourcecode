<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统提示</title>
<style type="text/css">
html{
	height:100%;
}
body{
	margin: 0px;
	padding: 0px;
	birder: 0;
	background-color: #f9f9f9;
	height:100%;
	font-size: 13px;
}
div{
	margin: 0px;
	padding: 0px;	
	border: 0;
}
img,form{
	border: 0;
}
a{
	color: #344463;
	font-size: 13px;
	text-decoration: none;
	font-weight: normal;
}
a:hover{
	text-decoration: none;
}
input, img{
	vertical-align: middle;	
}
.msgbox{
	position: absolute;
	background-image: url(__ITEM__/__IMG__/msg.gif);
	background-repeat: no-repeat;
	z-index: 90;
	height: 214px;
	width: 360px;
	left: 50%;
	top: 42%;
	margin-top: -107px;
	margin-left: -180px;
}
.msgbox .tit .l{
	color: #FFF;
	font-weight: bold;
	font-size: 14px;
	line-height: 34px;
	padding-left: 12px;
	width: 300px;
	float: left;
}
.msgbox .tit .r{
	float: left;
	width: 45px;
	text-align: center;
	padding-top: 9px;
	padding-bottom: 17px;
}
.msgbox .con{
	text-align: center;
	font-size: 15px;
	font-weight: bold;
	color: #344463;
}
.msgbox .con .m{
	line-height: 30px;
	padding-top: 24%;
	padding-top: 18%\9;
	_padding-top: 6%;
}
.msgbox .con .o{
}
</style>
</head>
<body>
<div class="msgbox">
 <div class="tit">
  <div class="l">系統提示信息</div>
  <div class="r"><a href="<?php echo($jumpUrl); ?>"><img alt="关闭" border="0" src="__ITEM__/__IMG__/close_our.png" width="8" height="8" /></a></div>
 </div>
 <div class="con">
 <present name="message">
 <div class="m"><?php echo($message); ?></div>
 <else/>
 <div class="m"><?php echo($error); ?></div>
 </present>
 <div class="o"><a id="href" href="<?php echo($jumpUrl); ?>"> <span id="wait" style="color:#F00"><?php echo($waitSecond); ?></span>秒后自动跳转，如果没有反应请点击此处</a></div>
 </div>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
if(wait.innerHTML==-1){
	var interval = setInterval(function(){
		location.href = href;
	}, 100);
}else{
	var interval = setInterval(function(){
		var time = --wait.innerHTML;
		if(time <= 0) {
			location.href = href;
			clearInterval(interval);
		};
	}, 1000);
}
})();
</script>
</body>
</html>