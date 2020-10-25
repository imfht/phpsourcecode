<?php
require '../conn.php';
require("../Appcode/conn.php");
require("clsAdmin_Info.php");
require 'global.func.php';



@$action = $_REQUEST["action"];
if ($action == "login")
{
    $sGetCode = $_REQUEST["getcode"];
    $username = trim($_REQUEST["username"]);
    $password = trim($_REQUEST["userpwd"]);



    $Admin = new Admin_Info();
    $user = $Admin->Login($username, $password);



    if ($user)
    {
        if ($DT['login_log']) $Admin->login_log($username, $password, 1);
        $forward="ad_index.php";
        if ($CFG['authadmin'] == 'cookie')
        {
            set_cookie($secretkey, $user['adminid']);
        } else
        {
            $_SESSION[$secretkey] = $user['adminid'];
        }
        dheader($forward);
    } else
    {
        if ($DT['login_log']) $Admin->login_log($username, $password, 1, $Admin->errmsg);
        msg($Admin->errmsg);
    }


}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理员登陆</title>
<LINK href="images/css.css" type=text/css rel=stylesheet>
<link rel="stylesheet" type="text/css" href="images/Style.css">
<style type="text/css">
body,td{
	font-size:14px;
}
.input0{
	width:180px;
	height:16px;
}
.inputpwd{
	width:80px;
	height:16px;
}
#showMsg{
	width:200px;
	position:absolute;
	top:10px;
	right:10px;
	border:solid 1px #666666;
	padding:5px;
	color:#FF0000;
	z-index:1000;
}
.STYLE1 {font-size: 12px}
</style>

<script language="javascript">
String.prototype.trim = function(){ return this.replace(/(^\s*)|(\s*$)/g, "");}
function ShowErrMsg(Info)
{
	document.getElementById("showMsg").innerHTML = Info;
}
function submitfrm(frm)
{
	if(frm.username.value.trim()=="")
	{
		 ShowErrMsg("用户名不能为空，请输入");
		 frm.username.focus();
		 return false;
	}
	if(frm.userpwd.value.trim()=="")
	{
		 ShowErrMsg("密码不能为空，请输入");
		 frm.userpwd.focus();
		 return false;
	}
	if(frm.getcode.value.trim()=="")
	{
		 ShowErrMsg("验证码不能为空，请输入");
		 frm.getcode.focus();
		 return false;
	}
	return true;
}
</script>
</head>

<body>
<div id="showMsg">欢迎使用，请登陆...</div>
<?php
if(isset($ErrMsg) )
{
?>
<script language="javascript">ShowErrMsg("<? echo $ErrMsg;?>");</script>
<?;}?>
<br />
<br />
<br />
<br />
<table cellspacing="0" cellpadding="0" width="420" align="center" border="0">
  <form id="form1" name="form1" method="post" action="" onSubmit="return submitfrm(this);">
    <tbody>
      <tr>
        <td><img height="36" src="images/login_admin1.gif"  width="420" /></td>
      </tr>
      <tr>
        <td><img height="106" src="images/login_admin2.gif"  width="420" /></td>
      </tr>
      <tr>
        <td width="420" background="images/login_admin3.gif" height="137">
            <table width="341" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td height="25">管理员帐号</td>
                    <td height="25"><input id="username" style="FONT-SIZE: 9pt; WIDTH: 120px; COLOR: black"
                                           maxlength="18" name="username"/></td>
                    <td height="25"><input id="Button1" type="submit" value="管理登陆" name="Button1"/></td>
                </tr>
                <tr>
                    <td height="25">管理员密码</td>
                    <td height="25"><input id="userpwd" style="FONT-SIZE: 9pt; WIDTH: 120px; COLOR: black"
                    type="password" maxlength="18" name="userpwd"/>
                    <input name="action" type="hidden" id="action"  value="login"/></td>
                    <td height="25"><input type="reset" name="Submit" value="清除再来"/></td>
                </tr>
                <tr>
                    <td height="25">程序验证码</td>
                    <td height="25">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="19%"><input id="getcode" style="WIDTH: 40px" maxlength="4" name="getcode"/>
                                </td>
                                <td width="81%">
                                    <img src="../AppCode/GetCode.php" width="80" height="20" border="0"
                                    style="cursor:hand;" title="没有看清？点击换一个..."
                                    onClick="javascript:this.src='../AppCode/GetCode.php';"/></td>
                            </tr>
                        </table>
                    </td>
                    <td height="25">

              <input onClick="window.location='../'" type="button" value="返回首页" name="Submit3"/>
            </td>
                </tr>
            </table>
        </td>
      </tr>
      <tr>
          <td><img height="51" src="images/admin_login.jpg" width="420"/></td>
      </tr>
    </tbody>
  </form>
</table>
</body>
</html>