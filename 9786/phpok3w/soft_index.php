
<?php



Const Base_Target = "target=""_blank"""
Const ChannelID = 3

?>
<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "vbs.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php
=Application(SiteID & "_Ok3w_SiteSoftTitle")
?></title>
<meta name="keywords" content="<?php
=Application(SiteID & "_Ok3w_SiteSoftKeyWords")
?>">
<meta name="description" content="<?php
=Application(SiteID & "_Ok3w_SiteSoftDescription")
?>">
<script language="javascript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav" style="margin-bottom:0px;">
  <tr>
    <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; 下载中心</td>
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
    <td valign="top" width="33%" style="padding-right:8px;"><div style="width:310px; height:265px; border:1px solid #CCC; margin-top:8px;">
      <?php
Call Ok3w_Soft_ImgFlash("",310,265)
?>
    </div></td>
    <td valign="top" width="33%" style="padding-right:8px;"><table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
      <tr>
        <td class="head"><h3 class="L"></h3>
              <span class="TAG">最新下载</span></td>
      </tr>
      <tr>
        <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
          <tr>
            <td><?php
Call Ok3w_Soft_List("",8,1,12,False,False,True,0,False,"new")
?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
    <td valign="top"><table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
      <tr>
        <td class="head"><h3 class="L"></h3>
              <span class="TAG">热门下载</span></td>
      </tr>
      <tr>
        <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
          <tr>
            <td><?php
Call Ok3w_Soft_List("",8,1,12,False,False,False,"",True,"hot")
?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable" style="margin-top:8px;">
  <tr>
    <td><?php
=GetAdSense(10)
?></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
<?php

Sql="select ID,SortName,gotoURL from Ok3w_Class where ChannelID=" & ChannelID & " and ParentID=0 and gotoURL='' order by OrderID"
Set oRs = Conn.Execute(Sql)
Do While Not oRs.Eof

?>
  <tr>
<?php
For i=1 To 3
?>  
    <td valign="top"<?php
If i<>3 Then
?> width="33%" style="padding-right:8px;"<?php
End If
?>>
<?php
If Not oRs.Eof Then
?>
<table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
  <tr>
    <td class="head"><h3 class="L"></h3>
        <span class="TAG"><a href="<?php
=Page_URL(ChannelID,oRs("ID"),"")
?>" target="_blank"><?php
=oRs("SortName")
?></a></span> <span class="more"><a href="<?php
=Page_URL(ChannelID,oRs("ID"),"")
?>" target="_blank">更多...</a></span></td>
  </tr>
  <tr>
    <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
      <tr>
        <td><?php
Call Ok3w_Soft_List(oRs("ID"),8,1,17,False,False,False,"",False,"new")
?></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php

oRs.MoveNext
End If
?>	</td>
<?php
Next
?>	
  </tr>
<?php

Loop
oRs.Close
Set oRs = Nothing

?>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="dragTable">
      <tr>
        <td class="head"><h3 class="L"></h3>
            <span class="TAG"><a href="http://www.ok3w.net/" target="_blank">友情连接</a></span> </td>
      </tr>
      <tr>
        <td class="middle">
		<div class="link"><?php
Call  Ok3w_Site_Link(27,9,1,2)
?><?php
Call  Ok3w_Site_Link(27,8,0,2)
?></div>
		</td>
      </tr>
    </table></td>
  </tr>
</table>  
<?php require_once "foot.php" ?>
</body>
</html>
