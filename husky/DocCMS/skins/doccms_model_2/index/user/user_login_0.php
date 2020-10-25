<style>
.shlcms_login { width:50px; height:20px; border:1px solid #ccc; -moz-border-radius:3px; border-radius:3px; cursor:pointer; background:-webkit-gradient(linear, 0 100%, 0 0, from(#E6E4E0), to(#ffffff)); background:-moz-linear-gradient(top, #ffffff, #E6E4E0); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0')"; }
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
  <form name="form1" id="form1" method="post" action="<?php echo sys_href($sid,'user','login'); ?>" onsubmit="return validator()">
    <table border="0" id="tbguest">
      <tr>
        <td align="right">用户名：</td>
        <td><input name="username" id="username" size="20" type="text" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'" />
          <font color="Red">*</font><span id="username_info"></span></td>
      </tr>
      <tr>
        <td align="right">密码：</td>
        <td><input name="pwd" id="pwd" size="20" type="password" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'" />
          <font color="Red">*</font><span id="pwd_info"></span></td>
      </tr>
      <tr>
        <td align="right">验证码：</td>
        <td><input name="checkcode" id="checkcode" size="8" type="text" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'" />
          <img src="<?php echo $tag['path.root']?>/inc/verifycode.php" /></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="Submit" name="Submit" value="登录" class="shlcms_login" />
          <a href="<?php echo sys_href($sid,'user','reg'); ?>">注册</a></td>
      </tr>
    </table>
  </form>
</div>