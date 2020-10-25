<?php require_once("chk.php"); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理系统</title>
<link rel="stylesheet" type="text/css" href="images/Style.css">
<meta http-equiv="refresh" content="600">
<style type="text/css">
<!--
.ttl{CURSOR: hand; COLOR: #000000; PADDING-TOP: 4px}
BODY{MARGIN-TOP: 5px; MARGIN-LEFT: 2px; BACKGROUND-COLOR: #fda700; TEXT-ALIGN: center}
td{line-height:170%;}
-->
</style>

<script language="javascript">
function showHide(obj)
{
	obj.style.display = obj.style.display == "none" ? "block" : "none";
}
</script>
</head>

<body>
<table cellspacing="1" cellpadding="2" width="150" align="center" bgcolor="#999999" border="0">
    <tbody>
    <tr>
        <td class="ttl" onClick="showHide(m0)" valign="top" align="left" background="images/top-bj3.jpg">
            <table cellspacing="0" cellpadding="0" width="127" border="0">
                <tbody>
                <tr>
                    <td width="8">&nbsp;</td>
                    <td align="left" width="117"><strong class="mtitle">常规操作</strong></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr id="m0">
        <td valign="top" align="middle" bgcolor="#f3f5f1">
            <table width="100%" cellpadding="2">
                <tbody>

                <tr>
                    <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                            href="ad_right.php" target="right">管理首页</a></td>
                </tr>
                <tr>
                    <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                            href="pass.php" target="right">修改密码</a></td>
                </tr>

                <tr>
                    <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                            href="quit.php" target="_parent" onClick="if(!confirm('真的要退出吗?')) return false;">安全退出</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<br>
 
    <table cellspacing="1" cellpadding="2" width="150" align="center" bgcolor="#999999"
           border="0">
        <tbody>
        <tr>
            <td class="ttl" onClick="showHide(menu6)" valign="top" align="left" background="images/top-bj3.jpg">
                <table cellspacing="0" cellpadding="0" width="127" border="0">
                    <tbody>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td align="left" width="117"><strong class="mtitle">系统管理</strong></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr id="menu6">
            <td valign="top" align="middle" bgcolor="#f3f5f1">
                <table width="100%" cellpadding="2">
                    <tbody>

                    <tr>
                        <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                                href="Sys_Config.php" target="right">站点信息配置</a></td>
                    </tr>
                    <tr>
                        <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                                href="Sys_admin.php" target="right">管理员管理</a></td>
                    </tr>

                    <tr>
                        <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                                href="Sys_db.php" target="right">数据库管理</a></td>
                    </tr>

                    <tr>
                        <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                                href="Sys_Ads.php" target="right">广告管理</a></td>
                    </tr>
                    <tr>
                        <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                                href="Sys_Vote.php" target="right">投票管理</a> <a href="Sys_Votelog.php"
                                                                               target="right">日志</a></td>
                    </tr>
                    <tr>
                        <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/><a
                                href="Sys_link.php" target="right">友情连接管理</a></td>
                    </tr>
                    <tr>
                        <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle"/>
                            <a href="ad_weblog.php" target="right">后台日志</a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
<br />
 
<table cellspacing="1" cellpadding="2" width="150" align="center" bgcolor="#999999" border="0">
  <tbody>
    <tr>
      <td class="ttl" onClick="showHide(m2)" valign="top" align="left" background="images/top-bj3.jpg">
        <table cellspacing="0" cellpadding="0" width="127" border="0">
        <tbody>
          <tr>
            <td width="8">&nbsp;</td>
            <td align="left" width="117"><span class="mtitle">新闻管理</span></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr id="m2" >
      <td valign="top" align="middle" bgcolor="#f3f5f1"><table width="100%" cellpadding="2">
        <tbody>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Class_Manage.php?ChannelId=1&amp;ParentID=0&amp;Depth=0" target="right">新闻分类</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Article_Edit.php?ChannelId=1" target="right">添加新闻</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Article_List.php?ChannelId=1" target="right">管理新闻</a> <a href="Article_List.php?ChannelId=1&IsPass=0" target="right" style="color:#0000FF;">待审</a> <a  href="Article_Move.php?ChannelId=1" target="right">转移</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Article_Code.php" target="right">外部调用代码</a></td>
          </tr>
		  <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Article_List.php?ChannelId=1&amp;IsDelete=1" target="right">回收站</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Pl_List.php?TypeID=2" target="right">评论管理</a> <a href="Pl_List.php?IsPass=0&TypeID=2" target="right" style="color:#0000FF;">待审</a></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
<br />
 
<table cellspacing="1" cellpadding="2" width="150" align="center" bgcolor="#999999" 
border="0">
  <tbody>
    <tr>
      <td class="ttl" onClick="showHide(m2)" valign="top" align="left" background="images/top-bj3.jpg"><table cellspacing="0" cellpadding="0" width="127" border="0">
        <tbody>
          <tr>
            <td width="8">&nbsp;</td>
            <td align="left" width="117"><span class="mtitle">软件管理</span></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr id="m2" >
      <td valign="top" align="middle" bgcolor="#f3f5f1"><table width="100%" cellpadding="2">
        <tbody>
          
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a href="Soft_Config.php" target="right">基本参数设置</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Class_Manage.php?ChannelId=3&ParentID=0&Depth=0" target="right">软件分类</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Soft_Edit.php?ChannelId=3" target="right">添加软件</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Soft_List.php?ChannelId=3" target="right">管理软件</a> <a  href="Article_Move.php?ChannelId=3" target="right">转移</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Soft_Code.php" target="right">外部调用代码</a></td>
          </tr>
		  <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Soft_List.php?ChannelId=3&IsDelete=1" target="right">回收站</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="Pl_List.php?TypeID=3" target="right">评论管理</a> <a href="Pl_List.php?IsPass=0&TypeID=3" target="right" style="color:#0000FF;">待审</a></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
<br />
 
<table cellspacing="1" cellpadding="2" width="150" align="center" bgcolor="#999999" border="0">
  <tbody>
    <tr>
      <td class="ttl" onClick="showHide(m3)" valign="top" align="left" background="images/top-bj3.jpg">
       <table cellspacing="0" cellpadding="0" width="127" border="0">
        <tbody>
          <tr>
            <td width="8">&nbsp;</td>
            <td align="left" width="117"><span class="mtitle">留言管理</span></td>
          </tr>
        </tbody>
      </table>
      </td>
    </tr>
    <tr id="m3" >
      <td valign="top" align="middle" bgcolor="#f3f5f1"><table width="100%" cellpadding="2">
        <tbody>
          <tr>
            <td align="left">
			<img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" />
			<a  href="Guest_List.php?IsPass=0" target="right" style="color:#0000FF;">等审留言</a></td>
          </tr>
          
          <tr>
            <td align="left">
			<img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" />
			<a  href="Guest_List.php" target="right">全部留言</a></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
<br />
 
<table cellspacing="1" cellpadding="2" width="150" align="center" bgcolor="#999999" 
border="0">
  <tbody>
    <tr>
      <td class="ttl" onClick="showHide(m3)" valign="top" align="left" background="images/top-bj3.jpg"><table cellspacing="0" cellpadding="0" width="127" border="0">
        <tbody>
          <tr>
            <td width="8">&nbsp;</td>
            <td align="left" width="117"><span class="mtitle">会员管理</span></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr id="m3" >
      <td valign="top" align="middle" bgcolor="#f3f5f1"><table width="100%" cellpadding="2">
        <tbody>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a href="Sys_ConfigSiteUserDengji.php" target="right">等级与积分</a></td>
          </tr>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" /><a  href="User_List.php" target="right">会员管理</a></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
<br />
 
<table cellspacing="1" cellpadding="2" width="150" align="center" bgcolor="#999999" 
border="0">
  <tbody>
    <tr>
      <td class="ttl" onClick="showHide(m1)" valign="top" align="left" background="images/top-bj3.jpg"><table cellspacing="0" cellpadding="0" width="127" border="0">
        <tbody>
          <tr>
            <td width="8">&nbsp;</td>
            <td align="left" width="117"><span class="mtitle">相关说明</span></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr id="m1" >
      <td valign="top" align="middle" bgcolor="#f3f5f1"><table width="100%" cellpadding="2">
        <tbody>
          <tr>
            <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" />
                <a  href="Class_Manage.php?ChannelId=2&ParentID=0" target="right">栏目管理</a></td>
          </tr>
<?php
$Sql="select * from Ok3w_Class where ChannelId=2 and gotoURL='' order by OrderID";
/*
Set oRs = Conn.Execute(Sql)
Do While Not oRs.Eof
Sql="select * from Ok3w_Article where ClassID=" & oRs("ID")
Rs.Open Sql,Conn,1,3
if( Rs.Eof And Rs.Bof )
{
	Rs.AddNew
	
	oMaxID = Conn.Execute("select max(ID) from Ok3w_Article")(0);
	if( IsNull(oMaxID) )
    oMaxID = 0;

	
	Rs("ID") =  oMaxID + 1
    Rs("ChannelID") = 2
    Rs("ClassID") = oRs("ID")
	Rs("SortPath") = "0," & oRs("ID") & ","
    Rs("Title") = oRs("SortName")
	Rs("TitleColor") = ""
	Rs("TitleURL") = ""
	Rs("Keywords") = ""
	Rs("Description") = ""
    Rs("Content") = oRs("SortName")
    Rs("Author") = "Systemp"
   	Rs("ComeFrom") = "Systemp"
    Rs("AddTime") = Now()
   	Rs("Inputer") = ""
    Rs("IsPass") = 1
    Rs("IsPic") = 0
    Rs("PicFile") = ""
   	Rs("IsTop") = 0
    Rs("IsCommend") = 0
	Rs("IsDelete") = 0
	Rs("IsMove") = 0
	Rs("IsPlay") = 0
	Rs("IsIndexImg") = 0
	Rs("IsUserAdd") = 0
	Rs("GiveJifen") = 0
	Rs("vUserGroupID") = 0
	Rs("vUserMore") = 1
	Rs("vUserJifen") = 0
    Rs("Hits") = 0
	Rs.Update
}
ID = Rs("ID");
Rs.Close;
*/
?>
<tr>
    <td align="left"><img height="7" hspace="5" src="images/arrow.gif" width="5" align="absmiddle" />
        <a  href="Article_Edit.php?ChannelId=2&action=edit&ID=<? // echo ID;?>" target="right"><? //echo oRs("SortName") ?></a>
    </td>
</tr>
<?php
/*
	oRs.MoveNext
Loop
oRs.Close
Set oRs = Nothing
*/
?>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
<br />
 

</body>
</html>