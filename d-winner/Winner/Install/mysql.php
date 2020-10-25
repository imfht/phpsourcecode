<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
if(file_exists('lock.txt')){
    echo '系统已安装，请不要重复安装！如需安装，请删除install文件夹下的lock.txt文件。';
    exit();
} 
 require_once (dirname(__FILE__) . "/inc/config.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Winner权限管理系统 - 安装向导</title>
<script language="javascript">
<!-- 
function onLast(){
	history.back();
}

function onClick(){
	h = list.host.value;
	n = list.name.value;
	u = list.user.value;
	wn = list.webname.value;
	hn = list.hostname.value;
	e = list.mail.value;
	au = list.adminuser.value;
	ap = list.adminpwd.value;
	ap2 = list.adminpwd2.value;
	if(h=="" || n=="" || u=="" || wn=="" || hn=="" || e=="" || au=="" || ap=="" || ap2==""){
		alert("选项不能为空");
		return false;
	}else{
		if(ap!=ap2){
			alert("两次输入的密码不相同");
			return false;
		}
		if(!e.match(/@/)){
			alert("邮箱格式不正确");
			return false;
		}
		
		return true;
	}
}
-->
</script>
<link href="img/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
 <form action="inc/install.act.php" method="post" name="list">
<div align="center">
<div class="main">
<div class="top">
  <img src="img/logo_about.png" height="45" />
  <span>Winner权限管理系统</span></div>
 
 <div class="content">
   <table width="100%" border="0" cellspacing="0">
     
  <tr>
    <td height="26" colspan="3" class="top_txt">数据库信息</td>
    </tr>
  <tr>
    <td width="21%" height="22">数据库服务器:</td>
    <td width="34%">&nbsp;<input name="host" type="text" class="txt_wd" value="127.0.0.1" /></td>
    <td width="45%">&nbsp;数据库服务器地址, 一般为 localhost</td>
    </tr>
  <tr>
    <td width="21%" height="22">数据库名:</td>
    <td width="34%">&nbsp;<input name="name" type="text" class="txt_wd" value="" /></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td width="21%" height="22">数据库用户名:</td>
    <td width="34%">&nbsp;<input name="user" type="text" class="txt_wd" value="" /></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td width="21%" height="22">数据库密码:</td>
    <td width="34%">&nbsp;<input name="pwd" type="password" class="txt_wd" value="" /></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td width="21%" height="22">数据表前缀:</td>
    <td width="34%">&nbsp;<input name="prefix" type="text" class="txt_wd" value="dwin_" /></td>
    <td>&nbsp;同一数据库运行多个系统时，请注意修改前缀</td>
    </tr>
  <tr>
    <td height="26" colspan="3" class="top_txt">程序及用户信息</td>
    </tr>
  <tr>
    <td width="21%" height="22">项目名称:</td>
    <td>&nbsp;<input name="webname" type="text" class="txt_wd" value="我的项目" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="21%" height="22">项目域名:</td>
    <td>&nbsp;<input name="hostname" type="text" class="txt_wd" value="<?php echo URL ?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="21%" height="22">管理员邮箱:</td>
    <td>&nbsp;<input name="mail" type="text" class="txt_wd" value="" /></td>
    <td>&nbsp;请务必填写正确</td>
  </tr>
  <tr>
    <td width="21%" height="22">管理员账号:</td>
    <td>&nbsp;<input name="adminuser" type="text" class="txt_wd" value="admin" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="21%" height="22">管理员密码:</td>
    <td>&nbsp;<input name="adminpwd" type="password" class="txt_wd" value="" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="21%" height="22">再输入一次密码:</td>
    <td>&nbsp;<input name="adminpwd2" type="password" class="txt_wd" value="" /></td>
    <td>&nbsp;请牢记您的账号与密码</td>
  </tr>

</table>
 </div> 
 <div class="act"><input onclick="onLast()" class="but" name="yes" type="button" value="上一步" /> &nbsp; <input onclick="return onClick()" class="but" name="put" type="submit" value="下一步" />
   
 <div><img src="img/step3.png" width="700" height="10" /></div>
 </div>
 <div class="foot">Copyright 2010-2015 <a href="http://www.95era.com/" target="_blank">九五时代</a> Inc.   All Rights Reserved</div>
</div>
</div></form>
</body>
</html>
