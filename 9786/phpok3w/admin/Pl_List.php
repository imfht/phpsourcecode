
<?php
Const dbdns="../"
?>
<?php require_once("chk.php");  ?>
<?php require_once("../AppCode/Pager.php");  ?>
<?php require_once("../AppCode/fun/function.php");  ?>
<?php require_once "../AppCode/fun/CreateHtml.php" ?>
<?php require_once "../AppCode/Class/Ok3w_Guest.php" ?>
<?php

TypeID = Request.QueryString("TypeID")
Select Case TypeID
	Case 2
		Call CheckAdminFlag(3)
	Case 3
		Call CheckAdminFlag(6)
	Case Else
		Response.End()
End Select

PageNo = Request.QueryString("PageNo")
If PageNo="" Then PageNo = "1"
IsPass = Request.QueryString("IsPass")
IdList = Request.Form("IdList")

If IdList<>"" Then
	Set Guest = New Ok3w_Guest
	
	Select Case Trim(Request.Form("Cmd"))
		Case "删除"
			Call Guest.Del(IdList)
			Call Guest.ArticleHTML(IdList,TypeID)
			Set Guest = Nothing
			Tmp = Split(IdList,",")
			Call SaveAdminLog("删除留言，ID=" & IdList)
			Call ActionOk("Pl_List.php?IsPass=" & IsPass & "&PageNo=" & PageNo & "&TypeID=" & TypeID)
		Case "开通"
				Call Guest.Pass(1,IdList)
				Call Guest.ArticleHTML(IdList,TypeID)
				Set Guest = Nothing
				Tmp = Split(IdList,",")
				Response.Write(IdList)
				Call SaveAdminLog("回复/开通/关闭留言，ID=" & IdList)
				Call ActionOk("Pl_List.php?IsPass=" & IsPass & "&PageNo=" & PageNo & "&TypeID=" & TypeID)
		Case "关闭"
				Call Guest.Pass(0,IdList)
				Call Guest.ArticleHTML(IdList,TypeID)
				Set Guest = Nothing
				Tmp = Split(IdList,",")
				Call SaveAdminLog("回复/开通/关闭留言，ID=" & IdList)
				Call ActionOk("Pl_List.php?IsPass=" & IsPass & "&PageNo=" & PageNo & "&TypeID=" & TypeID)
	End Select
End If

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>后台管理系统</title>
<link rel="stylesheet" type="text/css" href="images/Style.css">
<script language="javascript">
function ChkAll()
{
	var obj = document.form2.IdList;
	for(var i=0;i<obj.length;i++)
		obj[i].checked = !obj[i].checked;
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
                  <td class="mtitle" 
                background="images/tab_active_bg.gif">留言/评论管理</td>
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
Sql = "select * from Ok3w_Guest where 1=1 and TypeID=" & TypeID
If IsPass<>"" Then Sql=Sql & " and IsPass=" & IsPass
If keyword<>"" Then Sql = Sql & " and " & stype & " like '%" & keyword & "%'"
Sql = Sql & " order by Id desc"
Set Page = New TurnPage
Call Page.GetRs(Conn,Rs,Sql,10)

?>
                      <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
                        <form id="form1" name="form1" method="get" action="">
                          <tr>
                            <td height="30" colspan="6" align="left" bgcolor="#EBEBEB">&nbsp;
                              <select name="stype" id="stype">
                                <option value="UserName">按用户</option>
                              <option value="Title">按标题</option>
                              <option value="Content">按内容</option>
                              </select>
                                <input name="keyword" type="text" id="keyword" /><input name="TypeID" type="hidden" value="<?php
=TypeID
?>">
                                <input type="submit" name="Submit2" value="搜索" /></td>
                          </tr>
                        </form>
                        <tr>
                          <td height="25" align="center" bgcolor="#EBEBEB">发布者信息</td>
                          <td align="center" bgcolor="#EBEBEB">内容</td>
                          <td align="center" bgcolor="#EBEBEB">状态</td>
                          <td align="center" bgcolor="#EBEBEB">回复（点击回复）</td>
                          <td align="center" bgcolor="#EBEBEB">选择<br>
                            <input type="checkbox" onClick="ChkAll()"></td>
                          </tr>
                        <form id="form2" name="form2" method="post" action="?IsPass=<?php
=IsPass
?>&PageNo=<?php
=PageNo
?>&TypeID=<?php
=TypeID
?>">
                          <?php

	Do While Not Rs.Eof And Not Page.Eof
	
?>
                          <tr>
                            <td height="25" align="center" bgcolor="#FFFFFF">
                              <div style="width:200px; height:100px; overflow:hidden; border:1px solid #CCC; text-align:left;">姓名：<?php
= OutStr(Rs("UserName"))
?><br />标题：<?php
= OutStr(Rs("Title"))
?><br />邮箱：<?php
= OutStr(Rs("Mail"))
?><br />主页：<?php
= OutStr(Rs("Homepage"))
?><br />OICQ：<?php
= OutStr(Rs("QQ"))
?><br />时间：<?php
= Rs("AddTime")
?><br />IP：<?php
= Rs("Ip")
?></div></td>
                            <td align="center" bgcolor="#FFFFFF"><span style="color:#0000FF">评论：《<?php
If Rs("TypeID")=2 Then
?><?php
=ExecSqlReturnOneValue("select Title from Ok3w_Article where ID=" & Rs("TableID"))
?><?php
Else
?><?php
=ExecSqlReturnOneValue("select SoftName from Ok3w_Soft where ID=" & Rs("TableID"))
?><?php
End If
?>》</span>
                              <div style="width:350px; height:85px; overflow:auto; border:1px solid #CCC; text-align:left;"><?php
= OutStr(Rs("Content"))
?></div></td>
                            <td align="center" bgcolor="#FFFFFF"><?php
If Rs("IsPass") Then
?>开通<?php
Else
?><font color="#FF0000">关闭</font><?php
End If
?></td>
                            <td align="center" bgcolor="#FFFFFF"><a href="#" onClick="g_show(<?php
=Rs("ID")
?>)"><?php
If Rs("Ad_Ask")="" Then
?><font color="#FF0000">未回复</font><?php
Else
?>已回复<?php
End If
?></a></td>
                            <td align="center" bgcolor="#FFFFFF"><input name="IdList" type="checkbox" id="IdList" value="<?php
=Rs("ID")
?>"></td>
                            </tr> 
                          <?php

		Rs.MoveNext
		Page.MoveNext
	Loop
	Rs.Close
	
?>
                         
                          <tr>
                            <td height="25" colspan="6" align="right" bgcolor="#FFFFFF"><input name="Cmd" type="submit" id="Cmd" value="开通">
                                <input name="Cmd" type="submit" id="Cmd" value="关闭">
                                <input name="Cmd" type="submit" id="Cmd" onClick="if(!confirm('真的要删除吗？')) return false;" value="删除"></td>
                          </tr> </form>
                          <tr>
                            <td height="25" colspan="6" bgcolor="#FFFFFF"><?php
Call Page.GetPageList()
?></td>
                          </tr>
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
<div id="g_edit" style="z-index:9999; position:absolute; top:85px; left:20px; display:none;">
<iframe scrolling="auto" id="g_url" name="g_url" width="500" height="400" frameborder="1"></iframe>
<script language="javascript">
function g_show(id)
{
	document.getElementById("g_edit").style.display = "";
	g_url.location.href = "Guest_Edit.php?ID="+id +"&TypeID=2";
}
function g_hidde()
{
	document.getElementById("g_edit").style.display = "none";
}
</script>
</div>
</body>
</html>
<?php

Set Guest = Nothing
Set Rs = Nothing
Call CloseConn()
Set Admin = Nothing

?>

