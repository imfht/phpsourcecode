<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8" />
	<title>WCMS 登录</title>

	<!-- start: Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!-- end: Mobile Specific -->
	<meta http-equiv='Refresh' content='{$waitSecond};URL={$jumpUrl}'>
	
	<!-- start: CSS -->
	<link href="./static/bootstrap2/css/bootstrap.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/bootstrap-responsive.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/style.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/style-responsive.min.css" rel="stylesheet" />
	<link href="./static/bootstrap2/css/retina.css" rel="stylesheet" />
		<link href="./static/bootstrap2/css/my.less" rel="stylesheet/less" />
		<script type="text/javascript" src="./static/public/less.min.js" ></script>	<!-- end: CSS -->
	<!-- end: CSS -->
 <body>
		
</head>
{literal}
<style type="text/css">
.message{margin:10% auto 0px auto; padding:3px; border-collapse:collapse; background-color:#FFF; text-align:center; width:40%}
.row{width:100%;height:100px;padding-top:20px;}
</style>
{/literal}
<body style="background-color:#666;font-family:微软雅黑;">
<div class="message">
	<div style="width:100%;height:50px;background-color:#4d63a7">
	
	</div>
	<div class="row">
	   <div class="alt alert-block">{$message}</div>
		系统将在 <span style="color:red;font-weight:bold">{$waitSecond}</span> 秒后自动跳转,如果不想等待,直接点击 <a href="{$jumpUrl}">这里</a> 跳转
	</div>

</div>
</body>
</html>
