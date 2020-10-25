<?php if (!defined('THINK_PATH')) exit();?>﻿
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD><TITLE><?php echo (C("setting.Copyright")); ?> <?php echo (C("setting.Version")); ?> <?php echo (C("setting.Code")); ?></TITLE>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<LINK rel=stylesheet type=text/css href="/tuzicms/App/Manage/View/Default/css/login.css">
<SCRIPT type=text/javascript>
function check(fr)
{
  if(fr.user.value=="")
  {
     alert('管理帐号不能为空！');
	 fr.user.focus();
	 return false
  }
  if(fr.pass.value=="")
  {
     alert('登录密码不能为空！');
	 fr.pass.focus();
	 return false
  }
  if(fr.num.value=="")
  {
     alert('验证码不能为空！');
	 fr.num.focus();
	 return false
  }
  return true
}
window.onload = function(){
document.getElementById("user").focus();
}
</SCRIPT>
<script type="text/javascript">
	var verifyUrl="<?php echo U('Verify/verify','','');?>";
	$(function(){
		$('#vcode').click();
	});
	
	function change_code(){
	$("#vcode").attr("src",verifyUrl+'#'+Math.random());
	return false;
	}
</script>
<script type="text/javascript" src="/tuzicms/App/Manage/View/Default/js/jquery-1.7.2.min.js"></script>
<BODY id="login">
<div style="background:#CCCCCC">
<FORM id=loginForm onSubmit="return check(this)" method="post" action="/tuzicms/index.php/manage/login/do_login">

<H3>网站后台登录</H3>

<LABEL><SPAN>管理帐号：</SPAN><INPUT id="user"  class="input" maxlength="20" type="text" name="admin_name"> </LABEL>

<LABEL><SPAN>登录密码：</SPAN><INPUT id="pass" class="input" maxlength="20" type="password"  name="admin_pass"> </LABEL>

<LABEL><SPAN>验 证 码：</SPAN><INPUT id="num" class="input" maxlength="20" type="text"  name="verify"> </LABEL>

<LABEL><SPAN></SPAN><span style="margin-left:70px; margin-top:-10px;"><img src='/tuzicms/index.php/Manage/Verify/code?w=200&h=32' onclick='this.src=this.src+"?"+Math.randon'/></span></LABEL>


<br><br>
<DIV id="submit"><button class="submit" type="submit">立即登录</button></DIV>

</FORM>
</div>
<P id=siteCopyRight> <br>
<a href="http://www.tuzicms.com" target="_blank" style="text-decoration:none;">TuziCMS企业网站管理系统  <?php echo (C("setting.Version")); ?></a></P>
</BODY></HTML>