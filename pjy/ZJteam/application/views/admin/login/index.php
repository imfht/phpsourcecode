<?php 
	if(isset($error)){
		echo "<script>alert('$error')</script>";
	}
?>

<!DOCTYPE HTML>
<HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD><TITLE>守望网--后台登录</TITLE>
    <META content="text/html; charset=utf-8" http-equiv=Content-Type>
    <LINK rel=stylesheet type="text/css" href="<?php echo base_url()?>Public/admin_style/css/login.css">
<META name=GENERATOR content="MSHTML 8.00.6001.19412">
</HEAD>
<BODY style="BACKGROUND-COLOR: #f4f7f9" onload="document.getElementById('login-form').user.focus()">
<DIV class=login>
<P class=pl></P>
<DIV class=l></DIV>
<DIV class=c>
<FORM method="post" action="<?php echo site_url('admin/checklogin');?>" id="login-form">
<DIV class=to><SPAN class=tol><A class=v href="/" target=_blank></A></SPAN><SPAN class=tor>PJY</SPAN></DIV>
<DIV class=in>
<DL>
  <DT>用户名</DT>
  <DD><INPUT style="WIDTH: 150px" type="text" name="user"></DD>
  <DD class=e></DD></DL>
<DL>
  <DT>密　码</DT>
  <DD><INPUT style="WIDTH: 150px" type="password" name="passwd"></DD>
  <DD class=e></DD></DL>
<DL>
  <DT>验证码</DT>
  <DD><INPUT style="WIDTH: 80px" type=text name="code" onkeyup="if (this.value != this.value.toUpperCase()) this.value=this.value.toUpperCase();"  class="text-box"></DD>
  <DD style="PADDING-TOP: 5px"><img style="margin-bottom:14px;height:30px;width:80px;" title="点击刷新" src="<?php echo base_url();?>Public/image.php" align="absbottom" onclick="this.src='<?php echo base_url();?>Public/image.php?'+Math.random();"></img></DD>
  <DD class=e></DD></DL></DIV>
<DIV class=su><SPAN><INPUT class=go type="submit" value=""></SPAN><A 
href="<?php echo site_url('home/index')?>">返回首页</A></DIV></FORM></DIV>
<DIV class=r></DIV>
<P class=pr></P></DIV>
</BODY>
</HTML>
