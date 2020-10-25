
<?php



Const Base_Target = ""
Const ChannelID = 1

?>
<?php require_once "AppCode/Conn.php" ?>
<?php require_once "AppCode/fun/function.php" ?>
<?php require_once "AppCode/Class/Ok3w_Vote.php" ?>
<?php require_once "vbs.php" ?>
<?php

id=myCdbl(Request.QueryString("id"))
action = Request.Form("action")
kan = Request.QueryString("kan")
Set vote = New Ok3w_Vote
If action = "tou" Then
	If Not IsSelfRefer() Then Call MessageBox("请不要站外提交。","./")
	Call vote.Tou(id)
	Response.Redirect("vote.php?id=" & id & "&kan=yes")
End If
Call vote.Load(ID)

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php
=vote.Title
?></title>
<script language="javascript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0" class="nav">
  <tr>
    <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; 在线调查 &gt;&gt; <?php
=vote.Title
?></td>
  </tr>
</table>
<table width="960" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left" valign="top" style="border:1px solid #CCC; padding:28px;">
		
<!--投票结果统计-->	
<?php
If action="kan" Or kan="yes" Then
?>
<div style="border:1px solid #CCCCCC; margin-bottom:8px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
<?php

Sql = "select Title,[Value] from Ok3w_Vote where pID=" & vote.ID & " order by Xu"
Rs.Open Sql,Conn,0,1
v_Data=Rs.GetRows 
Rs.Close
v_Count=Ubound(v_Data,2)
v_Num = 0
s_Title = ""
s_Value = ""
For n=0 To v_Count
	s_Title = s_Title & "|||" & "选项" & (n + 1) 'Rs("Title")
	s_Value = s_Value & "|||" & v_Data(1,n)
	v_Num = v_Num + v_Data(1,n)
Next

?>
<div style="margin:25px 25px 0px 25px;">
<div style="border-bottom:1px solid #666666; padding:8px; line-height:170%;">
关于<strong style="color:#FF0000;"><?php
=vote.Title
?></strong>的投票结果：<br /><div style="color:#666666;">（共<?php
=v_Num
?>人参与投票）</div>
</div>
<?php
For n=0 To v_Count
?>
<div style="border-bottom:1px dotted #CCCCCC; padding:8px;">
选项<?php
=n+1
?>：<?php
=v_Data(0,n)
?>（<?php
=v_Data(1,n)
?>人，占<?php
=Round(v_Data(1,n)/v_Num * 100,1)
?>%）
</div>
<?php
Next
?>
</div>	
	</td>
    <td valign="top">
<div style="position:relative; width:450px; height:300px; overflow:hidden; ">
<div style="position:relative; top:-80px;">	
<!-- ampie script-->
<script type="text/javascript" src="images/ampie/swfobject.js"></script>
<div id="flashcontent">
	<strong>You need to upgrade your Flash Player</strong>
</div>
<script type="text/javascript">
  var rn = Math.random();
  var so = new SWFObject("images/ampie/ampie.swf", "ampie", "500", "500", "8", "#FFFFFF");
  so.addVariable("path", "images/ampie/");  
  so.addVariable("chart_id", "ampie"); // if you have more then one chart in one page, set different chart_id for each chart	
  so.addVariable("settings_file", encodeURIComponent("images/ampie/ampie_settings.xml?rn="+rn));
  so.addVariable("data_file", encodeURIComponent("images/ampie/ampie_data.php?v=<?php
=Server.URLEncode(s_Title & "###" & s_Value)
?>&rn="+rn));
  so.addVariable("preloader_color", "#999999");
  so.write("flashcontent");
</script>
<!-- end of ampie script -->
</div>
</div>
	</td>
  </tr>
</table>
</div>
<?php
End If
?>
<!--统计结果结束-->



<!--投票显示开始-->
<form id="frmvote" name="frmvote" method="post" action="?id=<?php
=vote.ID
?>">	
<div style="padding:10px 0px; font-size:14px; line-height:170%; border:1px solid #CCCCCC; margin-bottom:8px; padding:8px; background-color:#f2f6fb;">
<strong>欢迎你参与“<span style="color:#FF0000;"><?php
=vote.Title
?></span>”投票</strong>
<div style="font-size:12px; color:#666666;">（<?php
=vote.bTime
?> - <?php
=vote.eTime
?>）</div>
<?php
=OutStr(vote.Content)
?>
</div>

<?php

Sql = "select * from Ok3w_Vote where pID=" & vote.ID & " order by Xu"
Rs.Open Sql,Conn,1,1
n = 0
Do While Not Rs.Eof
n = n + 1

?>
<div style="border-bottom:1px dotted #CCCCCC; padding:8px;">
<?php
If vote.Value=0 Then
?>
  <input type="radio" name="vID" value="<?php
=Rs("ID")
?>" />
<?php
Else
?>
  <input name="vID" type="checkbox" id="vID" value="<?php
=Rs("ID")
?>" />
<?php
End If
?>
<span onClick="frmvote.vID[<?php
=n-1
?>].checked=true;" style="cursor:pointer;">选项<?php
=n
?>：<?php
=Rs("Title")
?></span>
</div>
<?php

	Rs.MoveNext
Loop
Rs.Close

?>
<br />
<input name="action" type="hidden" id="action" value="tou" />
<input name="Submit4" type="submit" value="投上一票" onClick="return chkvote(this.form);" style=" padding-top:4px; cursor:pointer;" />
<input name="Submit5" type="submit" value="看看投票统计" onClick="this.form.action.value='kan';" style=" padding-top:4px; cursor:pointer;" />
</form>
<script language="javascript">
function chkvote(frm)
{
	for(var i=0;i<frm.vID.length;i++)
		if(frm.vID[i].checked)
			return true;
	alert("请选择你的投票，谢谢！");
	return false;
}
</script>
<!--投票显示结束-->
				
			</td>
        </tr>
    </table>
<?php require_once "foot.php" ?>
</body>
</html>
