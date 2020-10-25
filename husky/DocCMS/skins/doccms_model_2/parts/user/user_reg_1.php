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
#tbguest { width:95%; text-align:left; clear:both; margin-top:10px; }
#tbguest td { padding-bottom:5px; }
#tbguest td.tdtxt { padding-left:5px; text-align:right; width:100px; }
#tbguest span{ color:#FF3300; margin:0 3px 0 8px;}
#tbguest input{ margin-left:8px;}
#tbguest select{ margin-left:8px;}
.useript{ -moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: white; border-color: #CCCCCC #E2E2E2 #E2E2E2 #CCCCCC; border-style: solid; border-width: 1px; box-shadow: 1px 2px 3px #F0F0F0 inset; color: #CCCCCC; height: 14px; overflow: hidden; padding: 10px 0 8px 8px; vertical-align: middle;}
.userbtn{ width:80px; height:40px; font-family:"微软雅黑"; font-size:20px; border:none; float:left; cursor:pointer; border:none;}
.userlog{ background:url(<?php echo $tag['path.skin']; ?>res/images/logbg.jpg) no-repeat; color:#fff; margin:0 45px 0 165px;}
</style>
<script language="JavaScript" type="text/javascript" src="<?php echo $tag['path.root']; ?>/inc/js/form/FormValid.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo $tag['path.root']; ?>/inc/js/form/FV_onBlur.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo $tag['path.root']; ?>/inc/js/form/valiUser.js"></script>
<script type="text/javascript">
<!--
FormValid.showError = function(errMsg,errName,formName) {
	for (key in FormValid.allName) {
		document.getElementById('errMsg_'+FormValid.allName[key]).innerHTML = '';
	}
	for (key in errMsg) {
		document.getElementById('errMsg_'+errName[key]).innerHTML = errMsg[key];
	}
}
-->
</script>
<div id="stuffbox">
<p><b>用户注册</b><br/></p>
<form name="form1" method="post" action="<?php echo sys_href($request['p'],'user','reg') ?>" onsubmit="return validator(this)">
<table border="0" id="tbguest">
  <tr>
    <td width="100" align="right"><label for="username"><span>*</span>用户名：</label></td>
    <td><input name="username" id="username" type="text" class="useript" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)" onkeyup="value=value.replace(/[\W]/g,'') "onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" onblur="return viliUser(this.value)"/><span id="authentication" style="color:#FF0000;"></span></td>
  </tr>
  <tr>
    <td align="right"><label for="pwd"><span>*</span>密码：</label></td>
    <td><input name="pwd" id="pwd" type="password" class="useript" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)" valid="required" errmsg="6到16位密码!" /><span id="errMsg_pwd" style="color:#FF0000"></span></td>
  </tr>
  <tr>
    <td align="right"><label for="repwd"><span>*</span>重复密码：</label></td>
    <td><input name="repwd" id="repwd" class="useript" type="password" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)" valid="eqaul" eqaulName="pwd" errmsg="两次密码不同!" /><span id="errMsg_repwd" style="color:#FF0000"></span></td>
  </tr>
  <tr>
    <td align="right"><label for="nickname">昵称：</label></td>
    <td><input name="nickname" id="nickname" class="useript" type="text" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)" /></td>
  </tr>
  <tr>
     <td align="right">姓名：</td>
     <td ><input name="name"  id="name" class="useript" type="text" value="<?php echo $user->name?>" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
  </tr>
  <tr>
    <td align="right"><span>*</span>性别：</td>
    <td >
      <select name="sex" style="width:45px;">
		<option value="1">男</option>
		<option value="2">女</option>
	  </select>
    </td>
  </tr>
  <tr>
    <td align="right">QQ：</td>
    <td><input name="qq" id="qq" type="text" class="useript" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
  </tr>
  <tr><td align="right">MSN：</td>
    <td><input name="msn" id="msn" type="text" class="useript" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
  </tr>
  <tr>
    <td align="right"><span>*</span>Email：</td>
    <td><input name="email" id="email" type="text" class="useript" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
  </tr>		
  <tr>
    <td align="right">手机：</td>
    <td><input name="mtel" id="mtel" type="text" class="useript" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
  </tr>	
  <tr>
    <td align="right">地址：</td>
    <td><input name="address" id="address" class="useript" type="text" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
  </tr>
  <tr>
    <td align="right">验证码：</td>
    <td><input name="checkcode" id="checkcode" class="useript" size="8" type="text" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'" /> <img src="<?php echo $tag['path.root']?>/inc/verifycode.php" /></td>
  </tr>
  <tr>
  	<td></td>
	<td><input type="submit" name="submit" value="注册" class="userbtn userlog"/></td>
  </tr>
</table>
</form>
</div>