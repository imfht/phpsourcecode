
<?php



Const Base_Target = ""
Const ChannelID = 1

?>
<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "AppCode/Pager.php" ?>
<?php require_once "AppCode/fun/md5.php" ?>
<?php require_once "AppCode/Class/Ok3w_User.php" ?>
<?php require_once "AppCode/Class/Ok3w_Article.php" ?>
<?php require_once "vbs.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>管理中心_<?php
=Application(SiteID & "_Ok3w_SiteName")
?></title>
<script language="javascript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<script language="javascript" src="images/DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
<style type="text/css">
.adminleft{border:1px solid #CCC; background-color:#F5F5F5; padding:8px; font-size:13px;}
</style>
</head>


<?php

ClassID = ""

Set User = New Ok3w_User
Set Article = New Ok3w_Article

User_Name = Trim(Replace(User.IsLogin(),"'",""))
If User_Name = "" Then
	Call MessageBox("你还没有登陆或是登陆已经超时。","./")
End If

a = Trim(Request.QueryString("a"))
b = Trim(Request.QueryString("b"))

If Trim(Request.QueryString("action")) = "LoginOut" Then
	Session(SiteID & "_Ok3w.Net_User_Name") = ""
	Response.Redirect("./")
End If

If a="user_edit" And b="save" Then
	Call  User.UserEdit() 
	Call MessageBox("操作成功","?a=user_edit&rnd=" & Now())
End If

If a="a_edit" And b="save" Then
	a_id = Trim(Request.QueryString("a_id"))
	Call Article.User_Article_Save(a_id)
	Call MessageBox("操作成功","?a=a_list&rnd=" & Now())
End If

If a="a_del" Then
	a_id = Cdbl(Request.QueryString("a_id"))
	Call Article.User_Article_Del(a_id,User_Name)
	Call MessageBox("操作成功","?a=a_list&rnd=" & Now())
End If

?>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
  <tr>
    <td>当前位置：<a href="./">网站首页</a> &gt;&gt; 个人管理中心</td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
  
  <tr>
    <td width="200" align="left" valign="top"><table width="190" border="0" cellspacing="0" cellpadding="10">
      <tr>
        <td align="center" class="adminleft" style="font-weight: bold">管理菜单</td>
      </tr>
      <tr>
        <td align="center" height="15"></td>
      </tr>
      <tr>
        <td align="center" class="adminleft"><a href="User_Index.php">个人资料</a></td>
      </tr>
      <tr>
        <td align="center" height="15"></td>
      </tr>
      <tr>
        <td align="center" class="adminleft"><a href="?a=a_edit">投稿/推荐文章</a></td>
      </tr>
      <tr>
        <td align="center" height="15"></td>
      </tr>
      <tr>
        <td align="center" class="adminleft"><a href="?a=a_list">管理文章</a></td>
      </tr>
      
      <tr>
        <td align="center" height="15"></td>
      </tr>
      <tr>
        <td align="center" class="adminleft"><a href="?action=LoginOut">安全退出</a></td>
      </tr>
    </table></td>
    <td align="left" valign="top">
	
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="box">
        
        <tr>
          <td valign="top"  style="padding:5px;">
<?php

Select Case a
	Case "a_edit"
		a_id = Trim(Request.QueryString("a_id"))
		Call Article_Edit(a_id)
	Case "a_list"
		Call Article_List()
	Case Else
		Call user_edit()
End Select

?>
<?php
Private Sub user_edit()
?>	  
		  <div style="padding:8px;border:1px solid #EBEBEB; background-color:#f2f6fb; font-size:14px; font-weight:bold;">个人资料</div>
		  <div style="border:1px solid #EBEBEB; margin-top:4px;">
		    
		      <table border="0" cellspacing="5" cellpadding="0">
		        <form id="form1" name="form1" method="post" action="?a=user_edit&b=save">
<?php

Sql = "select * from Ok3w_User where User_Name='" & User_Name & "'"
Rs.Open Sql,Conn,1,1

?>	  
<script language="javascript" src="images/DatePicker/WdatePicker.js"></script>			  
<script language="javascript">
function ChkReg(frm)
{
	if(frm.User_Password.value.trim()=="")
	{
		alert("原密码不能为空，请输入");
		frm.User_Password.focus();
		return false;
	}
	
	if(frm.User_Password21.value.trim()!="")
	{
		if(frm.User_Password21.value.trim().length<6 || frm.User_Password21.value.trim().length>20)
		{
			alert("新密码只能是6-20位字符");
			frm.User_Password21.focus();
			return false;
		}
		
		if(frm.User_Password21.value != frm.User_Password22.value)
		{
			alert("两次新密码输入不一致，请输入");
			frm.User_Password22.focus();
			return false;
		}
	}
	
	var exp2=/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+\.[a-zA-Z0-9._-]*$/
	if(frm.Mail.value.match(exp2)==null)
	{
		alert("电子邮件不正确，请输入");
		frm.Mail.focus();
		return false;
	}
	frm.bntSubmit.disabled=true;
	
	frm.submit();
}
</script>			  
                <tr>
                  <td colspan="2" class="red12">登陆信息（必填）</td>
                </tr>
                <tr>
                  <td align="right">用户名：</td>
                  <td><img src="<?php
=GetUserDengjiValue(2,GetUserDengjiID(Rs("Jifen")))
?>" /> <strong><?php
=Rs("User_Name")
?></strong> 注册时间：<strong><?php
=Rs("RegTime")
?></strong> 最后登陆：<strong><?php
= Rs("LastLoginTime")
?></strong></td>
                </tr>
                <tr>
                  <td align="right">积分：</td>
                  <td><h1><?php
=Rs("Jifen")
?></h1></td>
                </tr>
                <tr>
                  <td align="right">原密码：</td>
                  <td><input name="User_Password" type="password" id="User_Password" size="20" maxlength="20" />
                    <span class="red12">*</span>6-20位字符</td>
                </tr>
                <tr>
                  <td align="right">新密码：</td>
                  <td><input name="User_Password21" type="password" id="User_Password21" size="20" maxlength="20" />
                    <span class="red12">*</span>6-20位字符，不改请留空</td>
                </tr>
                <tr>
                  <td align="right">密认新密码：</td>
                  <td><input name="User_Password22" type="password" id="User_Password22" size="20" maxlength="20" />
                    <span class="red12">*</span></td>
                </tr>
                <tr>
                  <td align="right">邮箱：</td>
                  <td><input name="Mail" type="text" id="Mail" value="<?php
=Rs("Mail")
?>" size="35" maxlength="50" />
                    <span class="red12">*</span>请填最常用邮箱，方便联系</td>
                </tr>
                <tr>
                  <td colspan="2" class="red12">个人信息（选填）</td>
                </tr>
                <tr>
                  <td align="right">姓名：</td>
                  <td><input name="Name" type="text" id="Name" value="<?php
=Rs("Name")
?>" size="10" maxlength="8" /></td>
                </tr>
                <tr>
                  <td align="right">性别：</td>
                  <td><input type="radio" name="Sex" value="男" <?php
If Rs("Sex")="男" Then
?>checked="checked"<?php
End If
?>/>
                    男
                      <input type="radio" name="Sex" value="女" <?php
If Rs("Sex")="女" Then
?>checked="checked"<?php
End If
?>/>
                      女 
                      <input name="Sex" type="radio" value="保密" <?php
If Rs("Sex")="保密" Then
?>checked="checked"<?php
End If
?> />
                      保密</td>
                </tr>
                <tr>
                  <td align="right">出生年月日：</td>
                  <td><input name="Birthday" type="text" id="Birthday" onClick="WdatePicker()" value="<?php
= Rs("Birthday")
?>" size="10" maxlength="8" /></td>
                </tr>
                <tr>
                  <td align="right">电话：</td>
                  <td><input name="Tel" type="text" id="Tel" value="<?php
=Rs("Tel")
?>" size="15" maxlength="15" /></td>
                </tr>
                <tr>
                  <td align="right">QQ：</td>
                  <td><input name="QQ" type="text" id="QQ" value="<?php
=Rs("QQ")
?>" size="15" maxlength="15" /></td>
                </tr>
                <tr>
                  <td align="right">联系地址：</td>
                  <td><input name="Address" type="text" id="Address" value="<?php
=Rs("Address")
?>" size="35" maxlength="50" /></td>
                </tr>
                <tr>
                  <td align="right">邮编：</td>
                  <td><input name="Zip" type="text" id="Zip" value="<?php
=Rs("Zip")
?>" size="10" maxlength="6" /></td>
                </tr>
                <tr>
                  <td align="right">自我介绍：</td>
                  <td><textarea name="Content" cols="60" rows="8" id="Content"><?php
=Rs("Content")
?></textarea></td>
                </tr>
                
                <tr>
                  <td>&nbsp;</td>
                  <td><input name="bntSubmit" type="button" style="margin-top:15px; padding-top:5px; cursor:pointer;" id="bntSubmit" onClick="ChkReg(this.form);" value="Ok！立即修改" />
                    <input name="action" type="hidden" id="action" value="Reg" /></td>
                </tr>
     <?php
Rs.Close
?>        
</form>
              </table>
	      </div>
<?php
End Sub
?>	
<!--/////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<?php
Private Sub Article_List()
?>
<div style="padding:8px;border:1px solid #EBEBEB; background-color:#f2f6fb; font-size:14px; font-weight:bold; margin-bottom:4px;">管理文章</div>
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">				  
                        <tr style="border-bottom:1px solid #AACCEE; background-color:#EFEFEF; line-height:170%; text-align:center;">
                          <td style="border-bottom:1px solid #AEE1DC; background-color:#EFEFEF; line-height:170%; text-align:center;">标题</td>
                          <td style="border-bottom:1px solid #AEE1DC; background-color:#EFEFEF; line-height:170%; text-align:center;">时间</td>
                          <td style="border-bottom:1px solid #AEE1DC; background-color:#EFEFEF; line-height:170%; text-align:center;">状态</td>
                          <td style="border-bottom:1px solid #AEE1DC; background-color:#EFEFEF; line-height:170%; text-align:center;">操作</td>
                        </tr>
<?php

Sql = "Select * from Ok3w_Article where Inputer='" & Replace(User_Name,"'","") & "' order by ID desc"
Rs.Open Sql,Conn,1,1
If Not(Rs.Eof And Rs.Bof) Then
Do While Not Rs.Eof

?>						
                        <tr bgcolor="#FFFFFF">
                          <td style="line-height:170%; font-size:14px;"><?php
=Rs("Title")
?></td>
                          <td><?php
=Rs("AddTime")
?></td>
                          <td><?php
If Rs("IsPass")=1 Then
?>开通<?php
Else
?><font color="#FF0000">待审</font><?php
End If
?></td>
                          <td><?php
If Rs("IsPass")=0 Then
?><a href="?a=a_edit&a_id=<?php
=Rs("ID")
?>" style="text-decoration:underline;">修改/查看</a> <a href="?a=a_del&a_id=<?php
=Rs("ID")
?>" style="text-decoration:underline;" onClick="if(!confirm('真的要删除吗？')) return false;">删除</a>
                          <?php
Else
?>&nbsp;<?php
End If
?></td>
                        </tr>
<?php

	Rs.MoveNext
Loop
Else

?>						
                        <tr bgcolor="#FFFFFF">
                          <td colspan="4" align="center">暂无内容</td>
                        </tr>
<?php
End If
Rs.Close

?>
            </table>
<?php
End Sub
?>
<!--///////////////////////////////////////////////////////////////////////////////////////////-->	
<?php
Private Sub Article_Edit(ID)
If ID<>"" Then
	Call Article.Load(Cdbl(ID))
	If Article.Inputer<>User_Name Then
		Call MessageBox("对不起，你无法修改别人的文章","")
	End If
	If Article.IsPass=1 Then
		Call MessageBox("对不起，已经开通的文章，不能再修改","")
	End If
End If

?>
<div style="padding:8px;border:1px solid #EBEBEB; background-color:#f2f6fb; font-size:14px; font-weight:bold; margin-bottom:4px;">投稿/推荐文章</div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="5">	
<form id="form1" name="form1" method="post" action="?a=a_edit&b=save&a_id=<?php
=ID
?>">
<script language="javascript">
String.prototype.trim = function(){ return this.replace(/(^\s*)|(\s*$)/g, "");}
function ChkForm(frm)
{
	if(frm.Title.value.trim()=="")
	{
		alert("标题不能为空，请输入");
		frm.Title.focus();
		return false;
	}
	if(eWebEditor1.eWebEditor.document.body.innerHTML.trim()=="")
	{
		alert("内容不能为空，请输入");
		eWebEditor1.eWebEditor.focus();
		return false;
	}
	
	frm.bntSubmit.disabled = true;	
	frm.submit();
}
</script>			  
                        <tr bgcolor="#FFFFFF">
                          <td align="right">&nbsp;</td>
                          <td colspan="3" style="line-height:170%;">一、管理员审核后，将会公开显示出来；<br />
                            二、
                            审核通过后你将无法再对文章进行编辑；<br />
                          三、如果是转载，请认真注明来源和作者。</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">录入者</td>
                          <td colspan="3"><input name="textfield2" type="text" disabled="disabled" value="<?php
=User_Name
?>" size="25" maxlength="20" /></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td width="70" align="right">分类</td>
                          <td colspan="3"><select name="ClassID">
                            <?php
Call InitClassSelectOption(1,0,Article.ClassID)
?>
                          </select></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">标题<span class="red12">*</span></td>
                          <td colspan="3"><input name="Title" type="text" id="Title" value="<?php
=Article.Title
?>" size="50" maxlength="50" /></td>
              </tr>
                        

                        <tr bgcolor="#FFFFFF">
                          <td align="right">内容<span class="red12">*</span></td>
                          <td colspan="3">
						  <input name="Content" type="hidden" id="Content" value="<?php
=Server.HTMLEncode(Replace(Article.Content,"upfiles/","./upfiles/"))
?>" />
						  <input name="UpFiles" type="hidden" id="UpFiles">
						  <IFRAME ID="eWebEditor1" SRC="editor/ewebeditor.htm?id=Content&style=user&savepathfilename=UpFiles" FRAMEBORDER="0" SCROLLING="no" WIDTH="550" HEIGHT="300" style="border:1px solid #CCCCCC;"></IFRAME>							</td>
              </tr>
                        
                        <tr bgcolor="#FFFFFF">
                          <td align="right">来源</td>
                          <td><input name="ComeFrom" type="text" id="ComeFrom" value="<?php
=Article.ComeFrom
?>" size="25" maxlength="50" /></td>
                          <td align="right">作者</td>
                          <td><input name="Author" type="text" id="Author" value="<?php
=Article.Author
?>" size="25" maxlength="50" /></td>
                        </tr>
                        
                        
                        <tr bgcolor="#FFFFFF">
                          <td align="right">&nbsp;</td>
                          <td colspan="3"><input name="bntSubmit" type="button" style="margin-top:15px; padding-top:5px; cursor:pointer;" id="bntSubmit" onClick="ChkForm(this.form);" value="Ok！立即提交"/></td>
              </tr>
			  </form>
            </table>
<?php
End Sub
?>
<!--///////////////////////////////////////////////////////////////////////////////////////////-->  		  </td>
        </tr>
      </table>    </td>
  </tr>
</table>
<?php require_once "foot.php" ?>
</body>
</html>