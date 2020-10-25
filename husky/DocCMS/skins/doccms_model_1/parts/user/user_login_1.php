<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style>
.shlcms_login{ width:50px; height:20px; border:1px solid #ccc; -moz-border-radius:3px;border-radius:3px; cursor:pointer;
	background:-webkit-gradient(linear, 0 100%, 0 0, from(#E6E4E0), to(#ffffff));
	background:-moz-linear-gradient(top, #ffffff, #E6E4E0);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0');
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0')";	
}
#tbguest { width:95%; text-align:left; clear:both; margin-top:10px; }
#tbguest td { padding-bottom:5px; }
#tbguest td.tdtxt { padding-left:5px; text-align:right; width:100px; }
.useript{ -moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: white; border-color: #CCCCCC #E2E2E2 #E2E2E2 #CCCCCC; border-style: solid; border-width: 1px; box-shadow: 1px 2px 3px #F0F0F0 inset; color: #CCCCCC; height: 14px; overflow: hidden; padding: 10px 0 8px 8px; vertical-align: middle;}
.userbtn{ width:80px; height:40px; font-family:"微软雅黑"; font-size:20px; border:none; float:left; cursor:pointer;}
.userlog{ background:url(<?php echo $tag['path.skin']; ?>res/images/logbg.jpg) no-repeat; color:#fff; margin:0 45px 0 35px;}
.userreg{ background:url(<?php echo $tag['path.skin']; ?>res/images/regbg.jpg) no-repeat; color:#666; text-align:center; padding-top:10px;}
</style>
<script>
function validator()
{
 	if(document.getElementById('username').value=="")
	{alert("请输入用户名!"); document.getElementById('username').focus(); return false;}
	if(document.getElementById('pwd').value=="")
	{alert("请输入密码!");document.getElementById('pwd').focus();return false;}
	else{document.getElementById('form1').submit();}
}
</script>
<div id="stuffbox">
<p><b>用户登录</b></p>
<?php 
if(!empty($request['p'])){//用户自定义登录
	if(URLREWRITE)
	{
		$login='/'.$tag['channel.menuname'].'/action_login.html';
		$reg  ='/'.$tag['channel.menuname'].'/action_reg.html';
	}else{
		$login= './?p='.$request['p'].'&a=login&url='.urlencode($request['url']);
		$reg  ='./?p='.$request['p'].'&a=reg'; 
	}
}
?>
<form name="form1" id="form1" method="post" action="<?php echo $login; ?>" onsubmit="return validator()">
<table border="0" id="tbguest">
  <tr>
    <td align="right">用户名：</td>
    <td><input name="username" id="username" class="useript" size="20" type="text" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'" /> <font color="Red">*</font><span id="username_info"></span></td>
  </tr>
  <tr>
    <td align="right">密码：</td>
    <td><input name="pwd" id="pwd" size="20" class="useript" type="password" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'" /> <font color="Red">*</font><span id="pwd_info"></span></td>
  </tr>
  <tr>
    <td align="right">验证码：</td>
    <td><input name="checkcode" id="checkcode" class="useript" size="8" type="text" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'" /><img src="<?php echo $tag['path.root']?>/inc/verifycode.php" onClick="this.src='<?php echo $tag['path.root']?>/inc/verifycode.php?s='+Math.ceil(Math.random()*100000);" style="cursor:pointer" title="看不清？换一张"/></td>
  </tr>
  <tr>
  	<td></td>
	<td><input type="Submit" name="Submit" value="登录" class="userbtn userlog" />    
	<a href="<?php echo $reg;  ?>" class="userbtn userreg">注册</a></td>
  </tr>
</table>
</form>
</div>