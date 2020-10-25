
<?php



Const Base_Target = ""
Const ChannelID = 3

?>
<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "AppCode/Class/Ok3w_Soft.php" ?>
<?php require_once "vbs.php" ?>
<?php

id=myCdbl(Request.QueryString("id"))
Set Soft = New Ok3w_Soft
'Call Soft.HitsAdd(Id)
Call Soft.Load(Id)
If Soft.IsPass=0 Then Call Page_Err("软件已经关闭")
If Soft.IsDelete=1 Then Call Page_Err("软件已经删除")
ClassID = Soft.ClassID

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php
=Soft.SoftName
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
Call Ok3w_Soft_Class_Nav(Soft.SortPath)
?> &gt;&gt; 软件下载 </td>
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
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable" style="table-layout: fixed; word-wrap:break-word;">
  <tr>
    <td align="left" valign="top" style="border:1px solid #CCCCCC; padding:8px;">
	<div class="soft_s">
      <h1><?php
=Soft.SoftName
?></h1>
    </div>
        <div style="float:right;">
          <?php
If Soft.Softimageurl<>"" Then
?>
          <img src="<?php
=ReplaceUpFilePath(Soft.Softimageurl)
?>" alt="<?php
=Soft.SoftName
?>" width="336" onclick="window.open(this.src);" />
          <?php
Else
?>
          <?php
=GetAdSense(11)
?>
          <?php
End If
?>
        </div>
      <div style="line-height:180%;"> <strong>资源类别：</strong><?php
=GetClassName(Soft.ClassID)
?><br />
            <strong>软件版本：</strong><?php
=Soft.Softver
?><br />
            <strong>软件大小：</strong><?php
=Soft.Softsize
?><?php
=Soft.Softsizeunit
?><br />
            <strong>推荐等级：</strong><font color="#008000"><?php
Call SoftstarImg(Soft.Softstar)
?></font><br />
            <strong>资源语言：</strong><?php
=Soft.Softlanguage
?><br />
            <strong>授权方式：</strong><?php
=Soft.Softlicence
?><br />
            <strong>软件属性：</strong><?php
=Soft.Softproperty
?><br />
            <strong>应用平台：</strong><?php
=Soft.SoftTos
?><br />
            <strong>软件作者：</strong><?php
=Soft.Softauthor
?><br />
            <strong>作者主页：</strong><a href="http://<?php
=Soft.Softauthorurl
?>/" target="_blank"><?php
=Soft.Softauthorurl
?></a><br />
            <strong>演示地址：</strong><a href="http://<?php
=Soft.Softdemourl
?>/" target="_blank"><?php
=Soft.Softdemourl
?></a><br />
            <strong>下载次数：</strong><span id="downCount"><?php
=Soft.Hits
?></span><br />
            <strong>更新时间：</strong><?php
=soft.Updatetime
?>
		</div>
      <div style="width:100%;">
	  <script language="JavaScript" type="text/javascript">var softID = <?php
=Soft.ID
?>;</script>
      <script language="JavaScript" src="js/vote_s.js" type="text/javascript"></script>
	  </div>
        <div class="tit">
			<div class="tit_b">
				<strong>软件简介</strong>
			</div>
			<div class="tit_c">
				<div class="zoom">
					<?php
Call OutThisPageContent(Soft.ID,Soft.Softintro,"html")
?>
				</div>
			</div>
		</div>
		
		<a name="dl" id="dl"></a>
		<div class="tit">
			<div class="tit_b">
				<strong>下载地址</strong>
			</div>
			<div class="tit_c">
				<div class="zoom">
					<?php
If Application(SiteID & "_Ok3w_SiteSoftXunlei")<>"" Then
?>
                	<div class="downURL"><img src="images/pic_down_xunlei.gif" width="18" height="18" /><a href="c/download.php?SoftID=<?php
=Soft.ID
?>&SerID=0&UnionID=1" target="_blank">迅雷专用高速下载</a></div>
              		<?php
End If
?>
                	<?php
If Application(SiteID & "_Ok3w_SiteSoftFlashget")<>"" Then
?>
                	<div class="downURL"><img src="images/pic_down_flashget.gif" width="14" height="14" /> <a href="c/download.php?SoftID=<?php
=Soft.ID
?>&SerID=0&UnionID=2" target="_blank">快车专用高速下载</a></div>
              		<?php
End If
?>
                	<?php

				  	SevTmp = Split(Application(SiteID & "_Ok3w_SiteSoftServer"),vbCrLf)
				 	 For II=0 To Ubound(SevTmp)
				  	sTmp = Split(SevTmp(II),"|")
				  	
?>
                	<div class="downURL"><img src="images/pic_down.gif" width="14" height="14" /> <a href="c/download.php?SoftID=<?php
=Soft.ID
?>&SerID=<?php
=II
?>&UnionID=0" target="_blank"><?php
=sTmp(0)
?></a></div>
              	<?php
Next
?>
				</div>
			</div>
		</div>
		
		<div class="tit">
			<div class="tit_b">
				<strong>相关评论</strong>
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
                    <td class="vcode"><img src="c/validcode.php" alt="看不清？点击换一个" name="strValidCode" width="40" height="10" border="0" id="strValidCode" onclick="Get_ValidCode('./');"/></td>
                  </tr>
                </table>
              <input name="ValidCode" type="text" id="ValidCode" size="6" maxlength="4" />
                <br />
                <input name="bntSubmit" type="button" class="bbnt" id="bntSubmit" onclick="Ok3w_Book_Save(this.form,'./',3,<?php
=Soft.ID
?>);" value="Ok！立即提交" style="margin-top:15px; padding-top:5px; cursor:pointer;" />
                <br />
                <div style="margin-top:15px;">共有<strong><?php
=GetCommentsCount(3,Soft.ID)
?></strong>人对本文发表评论 <a href="./soft_comments.php?TableID=<?php
=Soft.ID
?>&TypeID=3" style="color:#990000; text-decoration:underline;">查看所有评论</a></div>
            </form>
				</div>
			</div>
		</div>
		
		<div class="tit">
			<div class="tit_b">
				<strong>下载说明</strong>
			</div>
			<div class="tit_c">
				<div class="zoom">
					<?php
=OutStr(Application(SiteID & "_Ok3w_SiteSoftDownHelp"))
?>
				</div>
			</div>
		</div>
    </td>
    <td width="346" align="right" valign="top"><?php require_once "soft_right.php" ?></td>
  </tr>
</table>
<?php require_once "foot.php" ?>
<script language="javascript">Ok3w_Soft_Hits("<?php
=Htmldns
?>",<?php
=Soft.ID
?>,"");</script>
</body>
</html>
