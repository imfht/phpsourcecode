<? require_once("AppCode/Conn.php"); ?>
<? require_once("AppCode/fun/function.php"); ?>
<? require_once("AppCode/Pager.php") ;?>
<? require_once("vbs.php") ;?>
<?
$ChannelID=1;
$ClassID = "";
$keyword=CmdSafeLikeSqlStr($_GET["q"]);
$sType = $_GET["t"];
if($keyword=="")
{
    MessageBox("请输入需要查询的关键词。","./");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <title><?=$keyword?> - <? Application(SiteID & "_Ok3w_SiteName") ?></title>
    <script language="javascript" src="js/js.js"></script>
    <script language="javascript" src="js/ajax.js"></script>
    <link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<? require_once("head.php");?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
    <tr>
        <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; “<%=OutStr(keyword)%>”搜索结果</td>
        <td align="right"><table border="0" cellspacing="2" cellpadding="0">
                <form id="form1" name="form1" method="get" action="search.asp">
                    <tr>
                        <td><input name="q" type="text" id="q" size="37" maxlength="255" /></td>
                        <td><input type="image" name="imageField" src="images/default/so.gif" style="border-width:0px;" /></td>
                    </tr>
                </form>
            </table></td>
    </tr>
</table>
<table width="960" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td align="left" valign="top" style="border:1px solid #CCC; padding:8px;">
            <div class="dragTable">
                <? Ok3w_Search_List($ClassID,$sType,$keyword,20); ?>
            </div>
        </td>
        <td width="346" align="right" valign="top">
            <? require_once("right.php");?>
             </td>
    </tr>
</table>
<? require_once("foot.php");?>
</body>
</html>
