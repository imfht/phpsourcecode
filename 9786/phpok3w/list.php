<?php
require 'conn.php';
$Base_Target = "target='_blank'";
$ChannelID = 1;
$SiteID=1;
require_once "AppCode/Conn.php";
require_once "AppCode/fun/function.php" ;
require_once "AppCode/Pager.php"  ;
require_once "vbs.php"  ;

$classid=intval($_GET["id"]);
$Sql="select * from Ok3w_Class where ID=" .$classid;
$mysqli=GetConn();
$result = $mysqli->query($Sql);
$row = $result->fetch_array(MYSQLI_ASSOC);

$SortPath = $row["sortpath"];
$SortName = $row["sortname"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php  Ok3w_Article_Class_PageTitle($SortPath);?> - <?php Application($SiteID & "_Ok3w_SiteName");?></title>
<script language="JavaScript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav" style="margin-bottom:0px;">
    <tr>
        <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; <?=Ok3w_Article_Class_Nav($SortPath);?></td>
        <td align="right">
            <table border="0" cellspacing="2" cellpadding="0">
                <form id="form1" name="form1" method="get" action="search.php">
                    <tr>
                        <td><input name="q" type="text" id="q" size="37" maxlength="255" /></td>
                        <td><input type="image" name="imageField" src="images/default/so.gif" style="border-width:0px;" /></td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
</table>



<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
    <tbody><tr>
        <td align="left" valign="top">

<?php

$psql="select * from ok3w_class where parentid=" . $classid . " and gotourl='' order by orderid";
$result = $db->query($psql);
while($info = $db->fetch_array($result))
{
    $page_url="/list.asp?id=".$info["id"];
    $showtitle=$info["sortname"];
?>

            <table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
                <tbody>
                <tr>
                    <td class="head">
                        <h3 class="L"></h3><span class="TAG">
                            <a href="<?=$page_url?>"><?=$showtitle?></a></span>
                        <span class="more">
                            <a href="<?=$page_url?>">更多...</a></span>
                    </td>
                </tr>
                <tr>
                    <td class="middle">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
                            <tbody>
                            <tr>
                                <td>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>

<?
$sql2="select id,title,content,titlecolor,titleurl,addtime from ok3w_article where ispass=1 and isdelete=0 and sortpath like '%," . $info["id"] . ",%' order by addtime desc,id desc limit 0,".$info['pagesize'];
$result2 = $db->query($sql2);
while ($info2 = $db->fetch_array($result2))
{
    $page_url2 = "/show.php?id=" . $info2["id"];
    $showtitle2 = $info2["title"];
    $addtime=$info2["addtime"];

?>

    <tr>
        <td class="list_title"><a href="<?=$page_url2?>" target="_blank"><?=$showtitle2?></a>
        </td>
        <td class="list_title_r"><?=$addtime?></td>
    </tr>

<?
}
?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

<?
}
?>



        </td>
        <td width="346" align="right" valign="top" style="padding-top:8px;">
            <?php include "right.php" ?>
        </td>
    </tr>
    </tbody></table>

<?php require_once "foot.php" ?>
</body>
</html>
