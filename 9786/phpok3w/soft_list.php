
<?php



Const Base_Target = "target=""_blank"""
Const ChannelID = 3

?>
<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "AppCode/Pager.php" ?>
<?php require_once "vbs.php" ?>
<?php

Set Page = New TurnPage
ClassID=myCdbl(Request.QueryString("id"))
Sql="select * from Ok3w_Class where ID=" & ClassID
Rs.Open Sql,Conn,0,1
SortPath = Rs("SortPath")
SortName = Rs("SortName")
Rs.Close

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php
Call Ok3w_Soft_Class_PageTitle(SortPath)
?> - <?php
=Application(SiteID & "_Ok3w_SiteName")
?></title>
<script language="JavaScript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
  <tr>
    <td><strong>当前位置：</strong><a href="./soft_index.php">下载首页</a> &gt;&gt; <?php
Call Ok3w_Soft_Class_Nav(SortPath)
?> &gt;&gt; 软件列表</td>
	<td align="right"><table border="0" cellspacing="2" cellpadding="0">
      <form id="form1" name="form1" method="get" action="./soft_search.php">
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
    <td valign="top">
	<?php
Call Ok3w_Soft_aList(ClassID,4,130,100,10)
?>
	</td>
    <td width="346" align="right" valign="top"><?php require_once "soft_right.php" ?></td>
  </tr>
</table>
<?php require_once "foot.php" ?>
</body>
</html>
