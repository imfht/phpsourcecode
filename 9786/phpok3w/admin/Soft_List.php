
<?php
Const dbdns="../"
?>
<?php require_once("chk.php");  ?>
<?php require_once("../AppCode/Pager.php");  ?>
<?php require_once("../AppCode/fun/function.php");  ?>
<?php require_once "../AppCode/Class/Ok3w_Soft.php" ?>
<?php

Call CheckAdminFlag(6)
ChannelID = Request.QueryString("ChannelID")
IsDelete = Request.QueryString("IsDelete")
If IsDelete = "" Then IsDelete = 0
IsUserAdd = Request.QueryString("IsUserAdd")

Set Soft = New Ok3w_Soft
Dim Cmd,CmdTmp
Cmd = Trim(Request.Form("Cmd"))
If Cmd <> "" Then
	CmdTmp = Split(Cmd,"|")
	Lid = Trim(Request.Form("Id"))
	Select Case CmdTmp(0)
		Case "pass"
			Call Soft.Pass(CmdTmp(1),Lid)
			Set Soft = Nothing
			Call SaveAdminLog("开通/关闭软件，Action=" & CmdTmp(1) & "，ID=" & Lid)
			Call ActionOk("Soft_List.php?ChannelID=" & ChannelID & "&IsDelete=" & IsDelete)
		Case "del"
			Call Soft.Del(Lid)
			Set Soft = Nothing
			Call SaveAdminLog("删除软件，ID=" & Lid)
			Call ActionOk("Soft_List.php?ChannelID=" & ChannelID & "&IsDelete=" & IsDelete)
		Case "top"
			Call Soft.Top(CmdTmp(1),Lid)
			Set Soft = Nothing
			Call SaveAdminLog("置顶/取消软件，Action=" & CmdTmp(1) & "，ID=" & Lid)
			Call ActionOk("Soft_List.php?ChannelID=" & ChannelID & "&IsDelete=" & IsDelete)
		Case "commend"
			Call Soft.Commend(CmdTmp(1),Lid)
			Set Soft = Nothing
			Call SaveAdminLog("推荐/取消软件，Action=" & CmdTmp(1) & "，ID=" & Lid)
			Call ActionOk("Soft_List.php?ChannelID=" & ChannelID & "&IsDelete=" & IsDelete)
		Case "Pic"
			Call Soft.Pic(CmdTmp(1),Lid)
			Set Soft = Nothing
			Call SaveAdminLog("图片/取消软件，Action=" & CmdTmp(1) & "，ID=" & Lid)
			Call ActionOk("Soft_List.php?ChannelID=" & ChannelID & "&IsDelete=" & IsDelete)
		Case "Okdel"
			Call Soft.OkDel(Lid)
			Set Soft = Nothing
			Call SaveAdminLog("彻底删除软件，ID=" & Lid)
			Call ActionOk("Soft_List.php?ChannelID=" & ChannelID & "&IsDelete=" & IsDelete)
		Case "Hf"
			Call Soft.Resumption(CmdTmp(1),Lid)
			Set Soft = Nothing
			Call SaveAdminLog("恢复，Action=" & CmdTmp(1) & "，ID=" & Lid)
			Call ActionOk("Soft_List.php?ChannelID=" & ChannelID & "&IsDelete=" & IsDelete)		
	End Select
End If

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>后台管理系统</title>
<link rel="stylesheet" type="text/css" href="images/Style.css">
<script language="javascript" src="../js/class.js"></script>
<script language="javascript" src="images/js.js"></script>
<script language="javascript">
var IsChkAll = false;
function ChkAll(frm)
{
	IsChkAll = !IsChkAll;
	for(var i=0; i<frm.elements.length; i++)
		if (frm.elements[i].type == "checkbox")
			frm.elements[i].checked = IsChkAll;
}

function a_edit(frm)
{
	var a_id=0;
	var a_count=0;
	for(var i=0; i<frm.elements.length; i++)
		if(frm.elements[i].name=="Id" && frm.elements[i].checked)
		{
			a_id = frm.elements[i].value;
			a_count ++;
		}
	if(a_count!=1)
		alert("请选择一个你需要修改/查看的软件");
		else
			document.URL="Soft_Edit.php?action=edit&Id=" + a_id + "&ChannelID=<?php
=ChannelID
?>";	
}

function a_copy(frm)
{
	var a_id=0;
	var a_count=0;
	for(var i=0; i<frm.elements.length; i++)
		if(frm.elements[i].name=="Id" && frm.elements[i].checked)
		{
			a_id = frm.elements[i].value;
			a_count ++;
		}
	if(a_count!=1)
		alert("请选择一个你需要复制的软件");
		else
			document.URL="Soft_Edit.php?action=copy&Id=" + a_id + "&ChannelID=<?php
=ChannelID
?>";
}

function a_action(frm,aStr)
{
	var a_count=0;
	for(var i=0; i<frm.elements.length; i++)
		if(frm.elements[i].name=="Id" && frm.elements[i].checked)
			a_count ++;
	if(a_count==0)
		alert("你需要至少选择一个软件进行相关操作");
		else
		{
			frm.Cmd.value = aStr;
			frm.submit();
		}
}
</script>
</head>

<body>
<?php require_once "top.php" ?>
<br />
<table cellspacing="0" cellpadding="0" width="98%" align="center" border="0">
  <tbody>
    <tr>
      <td style="PADDING-LEFT: 2px; HEIGHT: 22px" 
    background="images/tab_top_bg.gif"><table cellspacing="0" cellpadding="0" width="477" border="0">
        <tbody>
          <tr>
            <td width="147"><table height="22" cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td width="3"><img id="tabImgLeft__0" height="22" 
                  src="images/tab_active_left.gif" width="3" /></td>
                  <td 
                background="images/tab_active_bg.gif" class="tab"><strong class="mtitle">软件管理</strong></td>
                  <td width="3"><img id="tabImgRight__0" height="22" 
                  src="images/tab_active_right.gif" 
            width="3" /></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td bgcolor="#ffffff"><table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tbody>
          <tr>
            <td width="1" background="images/tab_bg.gif"><img height="1" 
            src="images/tab_bg.gif" width="1" /></td>
            <td 
          style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" 
          valign="top"><div id="tabContent__0" style="DISPLAY: block; VISIBILITY: visible">
              <table cellspacing="1" cellpadding="1" width="100%" align="center" 
            bgcolor="#8ccebd" border="0">
                <tbody>
                  <tr>
                    <td 
                style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" 
                valign="top" bgcolor="#fffcf7"><?php

stype = Trim(Request.QueryString("stype"))
keyword = Trim(Request.QueryString("keyword"))
ClassID = Trim(Request.QueryString("ClassID"))
IsPass = Trim(Request.QueryString("IsPass"))
IsPic = Trim(Request.QueryString("IsPic"))
IsTop = Trim(Request.QueryString("IsTop"))
IsCommend = Trim(Request.QueryString("IsCommend"))
IsMove = Trim(Request.QueryString("IsMove"))
IsPlay = Trim(Request.QueryString("IsPlay"))

Sql = "select * from Ok3w_Soft where ChannelID=" & ChannelID & " and IsDelete=" & IsDelete

If keyword<>"" Then Sql = Sql & " and " & stype & " like '%" & keyword & "%'"
If ClassID<>"" Then
	ClassID = Cdbl(ClassID)
	Sql = Sql & " and SortPath like '%," & ClassID & ",%'"
End If
If IsPass<>"" Then Sql = Sql & " and IsPass=" & Cint(IsPass)
If IsPic<>"" Then Sql = Sql & " and IsPic=" & Cint(IsPic)
If IsTop<>"" Then Sql = Sql & " and IsTop=" & Cint(IsTop)
If IsCommend<>"" Then Sql = Sql & " and IsCommend=" & Cint(IsCommend)
If IsMove<>"" Then Sql = Sql & " and IsMove=" & Cint(IsMove)
If IsPlay<>"" Then Sql = Sql & " and IsPlay=" & Cint(IsPlay)
If IsUserAdd<>"" Then Sql = Sql & " and IsUserAdd=" & Cint(IsUserAdd)

Sql = Sql & " order by Id desc"
Set Page = New TurnPage
Call Page.GetRs(Conn,Rs,Sql,20)

?>
                      <table width="98%" border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#CCCCCC">
                        <form id="Form" name="Form" method="get" action="">
                          <tr>
                            <td colspan="8" align="left" bgcolor="#EBEBEB"><select name="stype" id="stype">
                              <option value="SoftName">按标题</option>
                              <option value="Softintro">按内容</option>
                              </select>
							  <select name="ClassID"></select>
							  <script language="javascript">
						  InitSelect(document.Form.ClassID,"<?php
=ChannelId
?>","<?php
=ClassID
?>");
						  </script>
                                <input name="keyword" type="text" id="keyword" size="25" />
                                <input name="Submit" type="submit" class="bntStyle" value="查 找" />
                                <input name="ChannelID" type="hidden" id="ChannelID" value="<?php
=ChannelID
?>" />
                                <input name="IsDelete" type="hidden" id="IsDelete" value="<?php
=IsDelete
?>" />
                                <a href="?ChannelID=<?php
=ChannelID
?>">全部</a> <a href="?ChannelID=<?php
=ChannelID
?>&IsPass=0">待审</a> <a href="?ChannelID=<?php
=ChannelID
?>&IsTop=1">置顶</a> <a href="?ChannelID=<?php
=ChannelID
?>&IsCommend=1">推荐</a> <a href="?ChannelID=<?php
=ChannelID
?>&IsPic=1">图片</a> <a href="?ChannelID=<?php
=ChannelID
?>&IsMove=1">滚动</a> <a href="?ChannelID=<?php
=ChannelID
?>&IsPlay=1">轮播</a> </td>
                          </tr>
                        </form>
						<form id="form2" name="form2" method="post" action="?ChannelID=<?php
=ChannelID
?>&IsDelete=<?php
=IsDelete
?>">
                        <tr>
                          <td colspan="8" align="left" bgcolor="#EBEBEB"><?php
If IsDelete=0 Then
?><input name="bntEdit" type="button" class="bntStyle" id="bntEdit" onClick="a_edit(this.form)" value="修改/查看" />
                                 <input name="bntCopy" type="button" class="bntStyle" id="bntCopy" onClick="a_copy(this.form)" value="复 制" />
                                  <input name="bntDel" type="button" class="bntStyle" id="bntDel" onClick="a_action(this.form,'del|1')" value="删 除" />
                            <input name="bntPass" type="button" class="bntStyle" id="bntPass" onClick="a_action(this.form,'pass|1')" value="开 通" />
                            <input name="bntNotPass" type="button" class="bntStyle" id="bntNotPass" onClick="a_action(this.form,'pass|0')" value="关 闭" />
                            <input name="bntIsTop" type="button" class="bntStyle" id="bntIsTop" onClick="a_action(this.form,'top|1')" value="置 顶" />
                            <input name="bntNotIsTop" type="button" class="bntStyle" id="bntNotIsTop" onClick="a_action(this.form,'top|0')" value="取消置顶" />
                            <input name="bntIsCommend" type="button" class="bntStyle" id="bntIsCommend"  onclick="a_action(this.form,'commend|1')" value="推 荐" />
                            <input name="bntNotIsCommend" type="button" class="bntStyle" id="bntNotIsCommend" onClick="a_action(this.form,'commend|0')" value="取消推荐" /><?php
End If
?><?php
If IsDelete=1 Then
?>
                            <input name="bntIsPack" type="button" class="bntStyle" id="bntIsPack" onClick="a_action(this.form,'Okdel|1')" value="彻底删除" />
                            <input name="bntNotIsPic2" type="button" class="bntStyle" id="bntNotIsPic2" onClick="a_action(this.form,'Hf|1')" value="恢 复" /><?php
End If
?>
                            <input name="Cmd" type="hidden" id="Cmd" /></td>
                          </tr>
                        <tr>
                          <td align="center" bgcolor="#EBEBEB"><img src="images/formcheckbox.gif" width="20" height="20" style="cursor:hand;" onClick="javascript:ChkAll(forms[1]);" /></td>
                          <td align="center" bgcolor="#EBEBEB">编号</td>
                          <td align="center" bgcolor="#EBEBEB">标题</td>
                          <td align="center" bgcolor="#EBEBEB">栏目</td>
                          <td align="center" bgcolor="#EBEBEB">属性</td>
                          <td align="center" bgcolor="#EBEBEB">录入</td>
                          <td align="center" bgcolor="#EBEBEB">状态</td>
                          <td align="center" bgcolor="#EBEBEB">添加时间</td>
                          </tr>
                          <?php

If Not(Rs.Eof And Rs.Bof) Then						  
	Do While Not Rs.Eof And Not Page.Eof
	
?>
                          <tr>
                            <td align="center" bgcolor="#FFFFFF"><input name="Id" type="checkbox" id="Id" value="<?php
=Rs("Id")
?>" /></td>
                            <td align="center" bgcolor="#FFFFFF"><?php
=Rs("ID")
?></td>
                            <td align="center" bgcolor="#FFFFFF"><a href="Soft_Edit.php?action=edit&Id=<?php
=Rs("ID")
?>&ChannelID=<?php
=ChannelID
?>"><?php
= Rs("SoftName")
?></a></td>
                            <td align="center" bgcolor="#FFFFFF"><?php
=GetClassName(Rs("ClassID"))
?></td>
                            <td align="center" bgcolor="#FFFFFF">
                              <?php
If Rs("IsTop") Then
?>置顶 <?php
End If
?>
                              <?php
If Rs("IsCommend") Then
?>推荐 <?php
End If
?>
                              <?php
If Rs("Softimageurl")<>"" Then
?>图片 <?php
End If
?> 
							  <?php
If Rs("IsMove") Then
?>滚动 <?php
End If
?>
                              <?php
If Rs("IsPlay") Then
?>轮播 <?php
End If
?>
							   <?php
If Rs("IsDelete") Then
?>已删除 <?php
End If
?>                           </td>
                            <td align="center" bgcolor="#FFFFFF"><?php
=Rs("Inputer")
?></td>
                            <td align="center" bgcolor="#FFFFFF">
							<?php
If Rs("IsPass") Then
?>√<?php
Else
?><span style="color:red;">×</span><?php
End If
?>                              </td>
                            <td align="center" bgcolor="#FFFFFF"><?php
= Rs("Updatetime")
?></td>
                            </tr>
                          <?php

		Rs.MoveNext
		Page.MoveNext
	Loop
	
?>
                          
                          <tr>
                            <td height="25" colspan="8" align="right" bgcolor="#FFFFFF"><?php
Call Page.GetPageList()
?></td>
                          </tr>
<?php
Else
?>						  
						  <tr>
                            <td colspan="8" align="center" bgcolor="#FFFFFF">没有相关内容</td>
                          </tr>
<?php

End If
Rs.Close

?>						  
                        </form>
                      </table></td>
                  </tr>
                </tbody>
              </table>
            </div></td>
            <td width="1" background="images/tab_bg.gif"><img height="1" 
            src="images/tab_bg.gif" width="1" /></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td background="images/tab_bg.gif" bgcolor="#ffffff"><img height="1" 
      src="images/tab_bg.gif" width="1" /></td>
    </tr>
  </tbody>
</table>
</body>
</html>
<?php

Set Soft = Nothing
Set Rs = Nothing
Call CloseConn()
Set Admin = Nothing

?>

