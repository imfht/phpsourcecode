
<?php



Const Base_Target = ""
Const ChannelID = 1

?>
<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "AppCode/Class/Ok3w_Article.php" ?>
<?php require_once "vbs.php" ?>
<?php

ID=myCdbl(Request.QueryString("id"))
Set Article = New Ok3w_Article
'Call Article.HitsAdd(ID)
Call Article.Load(ID)
ClassID = ""

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php
=Article.Title
?></title>
<meta name="keywords" content="<?php
=Replace(Article.Keywords,"|",",")
?>" />
<meta name="description" content="<?php
=Article.Description
?>" />
<script language="javascript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
  <tr>
    <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; <?php
=Article.Title
?></td>
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
        <td align="left" valign="top" style="padding:8px; border:1px solid #CCC;">
	<div class="a_tit"><h1><?php
=Article.Title
?></h1>
    <div class="a_kk"><?php
=Format_Time(Article.AddTime,1)
?> 来源：<?php
=Article.ComeFrom
?> 浏览：<span id="News_Hits"><?php
=Article.Hits
?></span>次</div>
	</div>
	
	<div class="zoom">
	<?php
If Article.Description="" Then
?>
	<?php
Else
?>
	<div class="a_des"><strong>内容提要：</strong><?php
=OutStr(Article.Description)
?></div>
	<?php
End If
?>
	<?php
If Article.vUserGroupID=0 And Article.vUserJifen=0 Then
?>
	<?php
Call OutThisPageContent(Article.ID,Article.Content,"html")
?>
	<?php
Else
?>
	<iframe name="p_view" id="p_view" scrolling="No" src="c/p_view.php?id=<?php
=Article.ID
?>" height="200" onload="SetCwinHeight(this)" width="100%" frameborder="0"></iframe>
	<?php
End If
?>
	
	<div class="a_ad"><?php
=GetAdSense(5)
?></div>
	<div class="a_vote" style="border:0px;">
	<script language="javascript">var ArticleID = <?php
=Article.ID
?>;</script>
	<script language="javascript" src="js/vote_a.js"></script>
	</div>
		</td>
        <td width="346" align="right" valign="top"><?php require_once "right.php" ?></td>
      </tr>
    </table>
<?php require_once "foot.php" ?>
<script language="javascript">Ok3w_Article_Hits_Mood("<?php
=Htmldns
?>",<?php
=Article.ID
?>,"");</script>
</body>
</html>