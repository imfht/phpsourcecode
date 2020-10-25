<?php
require 'conn.php';
require "vbs.php";
require "AppCode/Conn.php" ;
require "AppCode/fun/function.php";

$Base_Target = "target='_blank'";
$ChannelID = 1;
$SiteID=1;


require_once "vbs.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo Application($SiteID & "_Ok3w_SiteTitle");?></title>
<meta name="keywords" content="<?php echo Application($SiteID & "_Ok3w_SiteKeyWords");?>">
<meta name="description" content="<?php echo Application($SiteID & "_Ok3w_SiteDescription"); ?>">
<script language="javascript" src="js/js.js"></script>
<script language="javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php require_once "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
  <tr>
    <td width="280" valign="top" style="padding-right:8px;"><div style="width:278px; height:215px; background-color:#FFF; border:1px solid #CCC;">
          <?php Ok3w_Article_ImgFlash("",278,215);?>
    </div>


        <div class="dragTable">
            <div class="middle" style="border-top-width:1px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <?
                    $sql="select id,title,TitleColor,TitleURL,Description from Ok3w_Article  ";
                    $sql.="where ChannelID=1 and IsPass=1 and IsDelete=0 and ismove=1  order by AddTime desc,ID desc limit 0,5";
                    $result = $db->query($sql);
                    while($info = $db->fetch_array($result))
                    {                        //循环输出结果集中的记录
                    ?>

                    <tr>
                        <td class="list_title"><a href="./show.php?id=<?=$info["id"]?>" target="_blank"><?=$info["title"]?></a></td>
                    </tr>
                    <?
                    }
                    ?>


                    </tbody>
                </table>
            </div>
        </div>





	</td>
    <td width="468" valign="top" style="padding-right:8px;">


        <table width="100%" border="0" cellspacing="0" cellpadding="0" id="IndexMainNews">
            <tbody><tr>
                <td class="L"></td>
                <td class="C">
                    <!--头条开始-->

                    <div class="top"><a href="./show.php?id=32" target="_blank">新闻头条，需要置顶即可</a></div>
                    <div class="D">新闻头条，需要置顶即可，新闻头条，需要置顶即可，新闻头条，需要置顶即可，新闻头条，需要置顶即可，新闻头条</div>

                    <!--头条结束-->
                    <div style="padding-bottom:8px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
<?
$sql="select Id,Title,TitleColor,TitleURL,Description from Ok3w_Article  ";
$sql.="where ChannelID=1 and IsPass=1 and IsDelete=0   order by AddTime desc,ID desc limit 0,7";
$result = $db->query($sql);
while($info = $db->fetch_array($result))
{                        //循环输出结果集中的记录
?>

    <tr>
        <td class="list_title">[<a href="./list.php?id=<?=$info['id']?>">小分类二</a>]
            <a href="./show.php?id=<?=$info['id']?>" target="_blank">新闻头条，需要置顶即可</a>
        </td>
        <td class="list_title_r">02-22</td>
    </tr>

<?php
}
?>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td class="R"></td>
            </tr>
            </tbody>
        </table>


	<div style="margin-top:5px;"><?php  Ok3w_Article_Gundong("",10,468,13,120);?></div>

	</td>
    <td valign="top">
	<div class="so">
	  <table border="0" cellspacing="2" cellpadding="0">
	    <form id="form1" name="form1" method="get" action="search.php">
          <tr>
            <td><input name="q" type="text" id="q" size="13" maxlength="255" /></td>
            <td><input type="image" name="imageField" src="images/default/so.gif" style="border-width:0px;" /></td>
          </tr>
	      </form>
	    </table>
	  </div>



        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="dragTable">
            <tbody><tr>
                <td class="head"><h3 class="L"></h3>
                    <span class="TAG">热门文章</span> </td>
            </tr>
            <tr>
                <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
                        <tbody><tr>
                            <td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td class="list_title"><a href="./show.php?id=32" target="_blank">新闻头条，需要置顶即</a></td></tr><tr><td class="list_title"><a href="./show.php?id=30" target="_blank">图片</a></td></tr><tr><td class="list_title"><a href="./show.php?id=24" target="_blank">滚动新闻</a></td></tr><tr><td class="list_title"><a href="./show.php?id=25" target="_blank">滚动新闻</a></td></tr><tr><td class="list_title"><a href="./show.php?id=22" target="_blank">滚动新闻</a></td></tr><tr><td class="list_title"><a href="./show.php?id=4" target="_blank">测试文章</a></td></tr><tr><td class="list_title"><a href="./show.php?id=15" target="_blank">图片新闻</a></td></tr><tr><td class="list_title"><a href="./show.php?id=13" target="_blank">图片新闻</a></td></tr><tr><td class="list_title"><a href="./show.php?id=14" target="_blank">图片新闻</a></td></tr><tr><td class="list_title"><a href="./show.php?id=28" target="_blank">测试</a></td></tr></tbody></table></td>
                        </tr>
                        </tbody></table></td>
            </tr>
            </tbody></table>






    </td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable" style="margin-top:8px;">
  <tr>
    <td style="border:1px solid #CCC; text-align:center;"><?php Ok3w_Article_ImgGD("",1,12,945,135,142,100,10,False,"new",120);?></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">

    <tbody><tr>

        <td valign="top" width="33%" style="padding-right:8px;">

<?
$sql="select id,sortname,gotourl from ok3w_class where channelid=1 and parentid=0 and gotourl='' order by orderid ";
$result = $db->query($sql);
$i=0;
while($info = $db->fetch_array($result))
{

    ?>

    <table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
        <tbody>
        <tr>
        <td class="head">
            <h3 class="L"></h3>
                    <span class="TAG">
                        <a href="./list.php?id=<?=$info['id']?>" target="_blank"><?=$info['sortname']?></a></span>
            <span class="more"><a href="./list.php?id=<?=$info['id']?>" target="_blank">更多...</a></span>
        </td>
        </tr>
        <tr>
            <td class="middle">

                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
                    <tbody><tr>
                        <td>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tbody>


<?
$classid=$info['id'];
$sql_i=" select id,classid,title,titlecolor,titleurl,addtime,hits from ok3w_article where channelid=1 and ispass=1 and isdelete=0 ";
$sql_i.=" and SortPath like '%,$classid,%' ";
$sql_i.=" order by hits desc,addtime desc,id desc";
$sql_i.=" limit 0,7 ";
//echo $sql_i;
$result_i = $db->query($sql_i);

while($info_i = $db->fetch_array($result_i))
{

?>
<tr>
    <td class="list_title"><a href="./show.php?id=<?=$info_i['id']?>" target="_blank"><?=$info_i['title']?></a>
    </td>
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
    $i++;

    if($i%3==0 && $i>0)
    {
        echo ' </tr><tr>';
    }


    echo   ' </td><td valign="top" width="33%" style="padding-right:8px;">';

}
?>







    </tr>



    </tbody></table>


<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="dragTable">
      <tr>
        <td class="head"><h3 class="L"></h3>
            <span class="TAG"><a href="http://www.ok3w.net/" target="_blank">友情连接</a></span> </td>
      </tr>
      <tr>
        <td class="middle">
		<div class="link"><?php echo  Ok3w_Site_Link(27,9,1,1);
echo  Ok3w_Site_Link(27,8,0,1);
?></div>
		</td>
      </tr>
    </table></td>
  </tr>
</table>
<?php require_once "foot.php" ?>
</body>
</html>
