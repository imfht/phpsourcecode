<?php
require 'conn.php';
require "vbs.php";
require "AppCode/Conn.php" ;
require "AppCode/fun/function.php";

$Base_Target = "target='_blank'";
$ChannelID = 1;
$SiteID=1;

$id=$_GET['id'];
$sql="select * from ok3w_article where id=".$id;
$result = $db->query($sql);
$info = $db->fetch_array($result);
$classid=$info["classid"];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$info['title']?></title>
    <meta name="keywords" content="<?=str_replace("|","",$info['keywords'])?>" />
    <meta name="description" content="<?=$info['description']?>" />
    <script language="JavaScript" src="js/js.js"></script>
    <script language="javascript" src="js/ajax.js"></script>
    <link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php include "head.php" ?>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
    <tr>
        <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; <a href="./list.asp?id=1">分类一</a> &gt;&gt; <a href="./list.asp?id=10">小分类二</a> &gt;&gt; 新闻头条，需要置顶即可</td>
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
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable" style="table-layout: fixed; word-wrap:break-word;">
    <tr>
        <td align="left" valign="top" style="border:1px solid #CCCCCC; padding:8px;">
            <div class="a_tit"><h1><?=$info['title']?></h1>
                <div class="a_kk"><?=$info['addtime']?> 来源：<?=$info['comefrom']?> 浏览：<span id="News_Hits"><?=$info['hits']?></span>次</div>
            </div>

            <div class="zoom">

                <div class="a_des"><strong>内容提要：</strong>新闻头条，需要置顶即可，新闻头条，需要置顶即可，新闻头条，需要置顶即可，新闻头条，需要置顶即可，新闻头条</div>

                <?=$info['content']?>
            </div>

            <div class="a_ad"> </div>
            <div class="a_vote">
                <script language="javascript">var ArticleID = 32;</script>

            </div>
            <div class="a_pn">
                上一篇：没有了<br /><a href="./show.asp?id=28" >下一篇：测试</a>
            </div>

            <div class="tit">
                <div class="tit_b">
                    <strong>相关文章</strong>
                </div>
                <div class="tit_c">
                    <div class="dragTable">
                        <div class="middle" style="border:0px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?
$sql2="select id,title,content,titlecolor,titleurl,addtime from ok3w_article where ispass=1 and isdelete=0 and sortpath like '%," . $info["id"] . ",%' order by addtime desc,id desc limit 0,8" ;
$result2 = $db->query($sql2);
while ($info2 = $db->fetch_array($result2))
{
$page_url2 = "/show.php?id=" . $info2["id"];
$showtitle2 = $info2["title"];
$addtime=$info2["addtime"];
?>
    <tr>
        <td class="list_title"><a href="<?=$page_url2?>"><?=$showtitle2?></a></td>
        <td class="list_title_r"><?=$addtime?></td>
    </tr>
<?
}
?>



                            </table></div>
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
                                        <input name="UserName" type="text" class="binput" id="UserName" size="12" maxlength="8" /></td>
                                    <td>&nbsp;</td>
                                    <td>联系QQ：<br />
                                        <input name="QQ" type="text" class="binput" id="QQ" size="12" maxlength="20" /></td>
                                    <td>&nbsp;</td>
                                    <td>邮箱：<br />
                                        <input name="Mail" type="text" class="binput" id="Mail" size="25" maxlength="100" /></td>
                                    <td>&nbsp;</td>
                                    <td>个人主页：<br />
                                        <input name="Homepage" type="text" class="binput" id="Homepage" size="25" maxlength="100" /></td>
                                </tr>
                            </table>
                            评论：<span class="red12">*</span><br />
                            <textarea name="Content" cols="78" rows="6" class="binput" id="Content"></textarea>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>验证：<span class="red12">*</span> → </td>
                                    <td class="vcode"><img src="c/validcode.asp" alt="看不清？点击换一个" name="strValidCode" width="40" height="10" border="0" id="strValidCode" onclick="Get_ValidCode('./');"/></td>
                                </tr>
                            </table>
                            <input name="ValidCode" type="text" class="binput" id="ValidCode" size="6" maxlength="4" />
                            <br />
                            <input name="bntSubmit" type="button" class="bbnt" id="bntSubmit" onclick="Ok3w_Book_Save(this.form,'./',2,32);" value="Ok！立即提交" style="margin-top:15px; padding-top:5px; cursor:pointer;" />
                            <br />
                            <div style="margin-top:15px;">共有<strong>0</strong>人对本文发表评论 <a href="./Comments.asp?TableID=32&TypeID=2" style="color:#990000; text-decoration:underline;">查看所有评论</a></div>
                        </form>
                    </div>
                </div>
            </div>

        </td>
        <td width="346" align="right" valign="top">
            <?php include "right.php" ?>
        </td>
    </tr>
</table>
<?php include "foot.php" ?>
<script language="javascript">Ok3w_Article_Hits_Mood("./",32,"");</script>
</body>
</html>