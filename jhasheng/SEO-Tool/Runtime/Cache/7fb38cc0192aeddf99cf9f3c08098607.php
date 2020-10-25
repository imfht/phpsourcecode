<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="Content-Language" content="zh-cn"/>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/metinfo.css">
<script type="text/javascript" src="__PUBLIC__/js/metinfo-min.js"></script>
<script type="text/javascript">
function pressCaptcha(obj){
    obj.value = obj.value.toUpperCase();
}
        intext.focus(function(){
		    $(this).addClass('metfocus');
		});
        intext.focusout(function(){
		    $(this).removeClass('metfocus');
		});
}
</script>
</head>
<style>
	html,body{  background:#fbfbfb;}
</style>
<body>
<div class="header" style="height:170px"></div>
<div class="login-min">
			<div class="login-left">
				<div style=" border-right:1px solid #fff; padding:0px 0px 20px;">
				<a href="" style="font-size:0px;" target="_blank" title="" class="img">
					<img src="" alt="" title="" />
				</a>
				<p>贾军军</p>
				</div>
			</div>
			<div class="login-right">	
				<h1 class="login-title">贾军军</h1>
				<div>
				<form method="post" action="<?php echo U('Index/login');?>" name="main_login" onSubmit="return check_main_login()">
					<input type="hidden" name="action" value="login" />
					<p><label>用户名：</label><input type="text" class="text" name="username" value="" /></p>
					<p><label>密码：</label><input type="password" class="text" name="password" /></p>
					<p class="login-code">
						<label>验证码：</label>
						<input name="code" onKeyUp="pressCaptcha(this)" type="text" class="text mid" id="code" />
						<img align="absbottom" src="<?php echo U('Index/verify');?>"  onclick=this.src="<?php echo U('Index/verify');?>&"+Math.random() style="cursor: pointer;" title=""/>
					</p>
					<p class="login-submit">
						<input type="submit" name="Submit" value="登陆" />
					</p>
				</form>
				</div>
			</div>
		<div class="clear"></div>
</div>
<div class="footer" style="margin-top:80px;"> </div>
<!--[if IE 6]>
<script src="<?php echo ($img_url); ?>/js/IE6-png.js" type="text/javascript"></script>
<script type="text/javascript">DD_belatedPNG.fix('.bg,img');</script>
<![endif]-->
</body>
</html>