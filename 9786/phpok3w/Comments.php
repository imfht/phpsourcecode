
<?php



Const Base_Target = ""
Const ChannelID = 1

?>
<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "AppCode/Class/Ok3w_Article.php" ?>
<?php require_once "AppCode/Pager.php" ?>
<?php require_once "vbs.php" ?>
<?php

TableID = Cdbl(Request.QueryString("TableID"))
TypeID = Cdbl(Request.QueryString("TypeID"))
Set Article = New Ok3w_Article
Call Article.Load(TableID)
ClassID = Article.ClassID

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>评论：<?php
=Article.Title
?></title>
<script language="javascript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
  <tr>
    <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; <a href="<?php
=Page_URL(ChannelID,"",TableID)
?>"><?php
=Article.Title
?></a> &gt;&gt; 评论</td>
	<td align="right"><table border="0" cellspacing="2" cellpadding="0">
      <form id="form1" name="form1" method="get" action="search.php">
        <tr>
          <td><input name="q" type="text" id="q" size="37" maxlength="255" /></td>
          <td><input type="image" name="imageField" src="images/default/so.gif" style="border-width:0px;" /></td>
        </tr>
      </form>
    </table></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
      <tr>
        <td style="padding:8px; border:1px solid #CCC;">
<?php

PageSize = 25
PageNo = Trim(Request.QueryString("PageNo"))
If PageNo="" Then PageNo = 1
LC = (PageNo-1) * PageSize
Sql = "select * from Ok3w_Guest where TypeID=2 and TableID=" & TableID
If Application(SiteID & "_Ok3w_SiteIsGuest")="1" Then Sql = Sql & " and IsPass=1"
Sql = Sql & " order by IsTop desc,ReTime desc,ID desc"
Set Page = New TurnPage
Call Page.GetRs(Conn,Rs,Sql,PageSize)

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="150" align="center">共有<strong><?php
=Rs.RecordCount
?></strong>篇评论</td>
    <td><?php
Call Page.GetPageList()
?></td>
  </tr>
</table>
<div class="ly_gg">评论：<?php
=Article.Title
?></div>
<?php

Do While Not Rs.Eof And Not Page.Eof
	LC = LC + 1
	b_head = Rs("pID")

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="ly_bb" style="table-layout: fixed; word-wrap:break-word;">
  <tr>
    <td width="150" align="center" valign="top"><img src="images/book/<?php
=b_head
?>.jpg" class="ly_bhead" /><br />
    <?php
=OutStr(Rs("UserName"))
?></td>
    <td valign="top">
	<div class="ly_rr"><span><?php
=LC
?></span>楼</div>
	<div class="ly_cc"><?php
=UBBCode(OutStr(Rs("Content")))
?>
	<?php
If Rs("Ad_Ask")<>"" Then
?><div class="ly_ask">管理员回复：<?php
=OutStr(Rs("Ad_Ask"))
?></div><?php
End If
?>
	</div>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><div class="ly_ll"><?php
=Rs("AddTime")
?></div></td>
  </tr>
</table>
<?php

	Rs.MoveNext
	Page.MoveNext
Loop

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="150" align="center">共有<strong><?php
=Rs.RecordCount
?></strong>篇留言</td>
    <td><?php
Call Page.GetPageList()
?></td>
  </tr>
</table>
<?php
Rs.Close
?>
<a name="sub" id="sub"></a>
		<div class="tit">
		<div class="tit_b">
			<strong>发表留言</strong>
		</div>
		<div class="tit_c">
			<div class="zoom">
						<form method="post" action="">
				        <table border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td>姓名：<span class="red12">*</span><br />
                                <input name="UserName" type="text" id="UserName" size="12" maxlength="8" /></td>
                            <td>&nbsp;</td>
                            <td>联系QQ：<br />
                                <input name="QQ" type="text" id="QQ" size="12" maxlength="20" /></td>
                            <td>&nbsp;</td>
                            <td>邮箱：<br />
                                <input name="Mail" type="text" id="Mail" size="25" maxlength="100" /></td>
                            <td>&nbsp;</td>
                            <td>个人主页：<br />
                                <input name="Homepage" type="text" id="Homepage" size="25" maxlength="100" /></td>
                          </tr>
                        </table>
评论：<span class="red12">*</span><br />
<textarea name="Content" cols="78" rows="6" id="Content"></textarea>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>验证：<span class="red12">*</span> → </td>
    <td class="vcode"><img src="./c/validcode.php" alt="看不清？点击换一个" name="strValidCode" width="40" height="10" border="0" id="strValidCode" onclick="Get_ValidCode('./');"/></td>
  </tr>
</table>
<input name="ValidCode" type="text" id="ValidCode" size="6" maxlength="4" />
                        <br />
                        <input name="bntSubmit" type="button" id="bntSubmit" onClick="Ok3w_Book_Save(this.form,'./',<?php
=TypeID
?>,<?php
=TableID
?>);" value="Ok！立即提交" style="margin-top:15px; padding-top:5px; cursor:pointer;" />
                      </form>
					  </div>
		</div>
	</div></td>
      </tr>
    </table>
<?php require_once "foot.php" ?>
</body>
</html>
