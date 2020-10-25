<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PJY - 提示信息</title>
<style>
body {font-family: arial, verdana; font-size: 9pt; word-break:break-all;}
#showmsg {
	margin:10% auto 0 auto;
	width:500px;
	height:150px;
	border:1px solid #c5ddf6;
	background: #fff url(<?php echo base_url()?>Public/admin_style/images/showmsg.gif) no-repeat 15px 30px;
}
#showmsg,#showmsg a {
	color:#2b3d63;
}
#showmsg a:hover {background:none;}
#showmsg #title{
	height:28px;
	line-height:28px;
	text-indent:5px;
	background:#eaf5ff;
}
#showmsg #content {
	float:right;
	height:80px;
	width:350px;
	margin:10px 0;
	padding:25px 10px 0px 10px;
	border-left: #dde0e6 1px solid;
}
#power {text-align:center;color:silver;margin:5pt;font-family:Verdana;}
</style>
</head>
<body>
<div id="showmsg">
 <div id="title">PJY- 提示信息</div>
 <div id="content"><?php echo $msg?><br /><br /><a onclick="history.go(-1)" href="javascript:void(0)">如果您的浏览器没有自动跳转，请点击这里</a></div>
</div>
<div id="power">PJY消息提示</div>
</body>
</html>
