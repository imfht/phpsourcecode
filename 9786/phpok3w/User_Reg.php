<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "AppCode/Pager.php" ?>
<?php require_once "vbs.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>会员注册 - <%=Application(SiteID & "_Ok3w_SiteName")%></title>
<script language="javascript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<script language="javascript" src="images/DatePicker/WdatePicker.js"></script>	
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<? require_once("head.php"); ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
  <tr>
    <td><strong>您当前所在位置：</strong><a href="./">网站首页</a> &gt;&gt; 会员免费注册</td>
  </tr>
</table>
<table width="960" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
  <tr>
    <td align="left" valign="top">
		  <div style="border:1px solid #CCCCCC;">
		     <div style="background-color:#F5F5F5; padding:8px; font-size:14px;"><strong>欢迎加入我们^_^</strong></div>
			 <div style="padding:8px;">
		      <table border="0" cellspacing="5" cellpadding="0">
		        <form  method="post" action="">			  
                <tr>
                  <td colspan="2" class="red12">登陆信息（必填）</td>
                </tr>
                <tr>
                  <td align="right">用户名：</td>
                  <td><input name="User_Name" type="text" id="User_Name" size="20" maxlength="20" />
                    <span class="red12">*</span>4-20位字符，可以是中文</td>
                </tr>
                <tr>
                  <td align="right">密码：</td>
                  <td><input name="User_Password" type="password" id="User_Password" size="20" maxlength="20" />
                    <span class="red12">*</span>6-20位字符</td>
                </tr>
                <tr>
                  <td align="right">密认密码：</td>
                  <td><input name="User_Password2" type="password" id="User_Password2" size="20" maxlength="20" />
                    <span class="red12">*</span></td>
                </tr>
                <tr>
                  <td align="right">邮箱：</td>
                  <td><input name="Mail" type="text" id="Mail" size="35" maxlength="50" />
                    <span class="red12">*</span>请填最常用邮箱，方便联系</td>
                </tr>
                <tr>
                  <td colspan="2" class="red12">个人信息（选填）</td>
                </tr>
                <tr>
                  <td align="right">姓名：</td>
                  <td><input name="Name" type="text" id="Name" size="10" maxlength="8" /></td>
                </tr>
                <tr>
                  <td align="right">性别：</td>
                  <td><input type="radio" name="Sex" value="男" />
                    男
                      <input type="radio" name="Sex" value="女" />
                      女 
                      <input name="Sex" type="radio" value="保密" checked="checked" />
                      保密</td>
                </tr>
                <tr>
                  <td align="right">出生年月日：</td>
                  <td><input name="Birthday" type="text" id="Birthday" size="10" maxlength="8" onClick="WdatePicker()" readonly="readonly" /></td>
                </tr>
                <tr>
                  <td align="right">电话：</td>
                  <td><input name="Tel" type="text" id="Tel" size="15" maxlength="15" /></td>
                </tr>
                <tr>
                  <td align="right">QQ：</td>
                  <td><input name="QQ" type="text" id="QQ" size="15" maxlength="15" /></td>
                </tr>
                <tr>
                  <td align="right">联系地址：</td>
                  <td><input name="Address" type="text" id="Address" size="35" maxlength="50" /></td>
                </tr>
                <tr>
                  <td align="right">邮编：</td>
                  <td><input name="Zip" type="text" id="Zip" size="10" maxlength="6" /></td>
                </tr>
                <tr>
                  <td align="right">自我介绍：</td>
                  <td><textarea name="Content" cols="68" rows="10" id="Content"></textarea></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td><textarea name="textarea" cols="68" rows="10"><%=Application(SiteID & "_Ok3w_SiteUserRegTK")%></textarea></td>
                </tr>
                <tr>
                  <td align="right">验证码：</td>
                  <td>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="ValidCode" type="text" id="ValidCode" size="6" maxlength="4" /><span class="red12">*</span></td>
	<td>&nbsp;请输入→&nbsp;</td>
    <td><img src="./c/validcode.php" alt="看不清？点击换一个" name="strValidCode" width="40" height="10" border="0" id="strValidCode" onclick="Get_ValidCode('./');" class="vcode" /></td>
  </tr>
</table></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td><input name="bntSubmit" type="button" class="bbnt" id="bntSubmit" onClick="Ok3w_User_Reg(this.form,'./');" value="同意以上《注册条款》并提交注册" style="margin-top:15px; padding-top:5px; cursor:pointer;" /></td>
                </tr>
                
</form>
              </table>
             </div>
		    </div>
    </td>
    <td width="346" align="right" valign="top">
		  <?php require_once "right.php" ?>
    </td>
  </tr>
</table>
<?php require_once "foot.php" ?>
</body>
</html>