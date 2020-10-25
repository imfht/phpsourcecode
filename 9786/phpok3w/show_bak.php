<?php include "conn.php";?>
<?php
if (!isset($_GET["id"]))
{
    echo "参数错误";
    exit();
}
$id=intval($_GET["id"]);
$prosql=mysql_query("select * from xinghao where id=$id");
$prorow=mysql_fetch_array($prosql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$prorow['xinxinghao'].$prorow['leixing']?>－型号查询</title>
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <link href='http://www.zcwz.com/style/global.css' type='text/css' rel='stylesheet' />
    <meta name="description" content="<?=$prorow['xinxinghao'].$prorow['leixing']?>">
    <meta name="keywords" content="<?=$prorow['xinxinghao'].$prorow['leixing']?>">
    <script language="javascript" type="text/javascript" src="http://www.zcwz.com/javascript/function.js"></script>
    <script language="javascript" type="text/javascript" src="http://www.zcwz.com/javascript/comm.js"></script>
    <script src="http://www.zcwz.com/new-_zcpp/js/jquery.js" language="JavaScript" type="text/javascript"></script>
    <script src="http://www.zcwz.com/new-_zcpp/js/auntion.js" language="JavaScript" type="text/javascript"></script>
    <link href="/css/css.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="http://www.zcwz.com/style/index_head.css">
    <link rel="stylesheet" type="text/css" href="http://www.zcwz.com/style/toolbar.css" />
</head>

<body>
<?php require_once "head.php" ?>
<script type="text/javascript" src="http://www.zcwz.com/js/comm-sp1-min.js"></script>
<a id="tagOpenWin" target="_blank"></a>
<div class="tlbg">
    <div id="toolbar">
        <ul class="bar_right">
            <li><a href="http://www.zcwz.com" target="_blank">中华轴承网首页</a></li>
            <li class="lines"></li>
            <li><a href="http://www.zcwz.com/fuwu.asp" target="_blank">会员中心</a></li>
            <li class="lines"></li>
            <li><a href="http://www.zcwz.com/new-_vipfuwu/" target="_blank">财富宝</a></li>
            <li class="lines"></li>
            <li><a href="http://www.zcwz.com/pm/" target="_blank">排名三甲</a></li>
            <li class="lines"></li>
            <li><a href="http://www.zcwz.com/paihang/" target="_blank">活跃度排行</a></li>
            <li class="lines"></li>
            <li><a href="http://www.zcwz.com/new-_zcpp/">品牌分类</a></li>
            <li class="lines"></li>
            <li id="tl_nav" class="tl_more">
                <a class="a_nav" href="javascript:" target="_self">网站导航</a>
                <div class="more_nav">
                    <h5><a href="javascript:" target="_self">网站导航</a></h5>
                    <div class="more_navlist">
                        <p>
                            <a class="title" target="_blank" href="http://s.zcwz.com">精创商城</a>
                            <a target="_blank" href="http://www.zcwz.com/gonghuo/index.asp?type=zc">轴承</a>
                            <a target="_blank" href="http://www.zcwz.com/gonghuo/index.asp?type=jiqi">轴承设备</a>
                            <a target="_blank" href="http://www.zcwz.com/gonghuo/index.asp?type=peijian">轴承配件</a>
                            <a target="_blank" href="http://www.zcwz.com/gonghuo/index.asp?type=yibiao">轴承仪器</a>
                            <a target="_blank" href="http://steel.zcwz.com/">轴承钢材</a>
                        </p>
                        <p>
                            <a class="titlea" target="_blank" href="http://www.zcwz.com/qiugou">求购</a>
                            <a class="titlea" target="_blank" href="http://www.zcwz.com/shangcheng">企业</a>
                            <a class="titlea" target="_blank" href="http://zc.zcwz.com">进口轴承</a>
                            <a class="titlea" target="_blank" href="http://zcpt.zcwz.com">配套</a>
                            <span class="clefix"></span>
                        </p>
                        <p class="mt5">
                            <a class="title" target="_blank" href="http://c.zcwz.com">型号查询</a>
                            <a target="_blank" href="http://c.zcwz.com/index.asp">轴承型号查询</a>
                            <a target="_blank" href="http://c.zcwz.com/index1.asp">国内外型号对照</a>
                            <a target="_blank" href="http://c.zcwz.com/index2.asp">轴承样本</a>
                        </p>
                        <p class="mt5" style="border:none;">
                            <a class="titlea tit_p" target="_blank" href="http://news.zcwz.com">资讯</a>
                            <a class="titlea" target="_blank" href="http://zhishi.zcwz.com">轴承知识</a>
                            <a class="titlea" target="_blank" href="http://zhishi.zcwz.com/loreList_0_1.html">贸易知识</a>
                        </p>
                        <p>
                            <a class="titlea" target="_blank" href="http://zhanhui.zcwz.com">展会</a>
                            <a class="titlea tit_p" target="_blank" href="http://bbs.zcwz.com">论坛</a>
                            <a class="titlea" target="_blank" href="http://rc.zcwz.com">人才</a>
                            <a class="titlea" target="_blank" href="http://www.zcwz.com/new-_about/youqing_more.asp">友情链接</a>
                            <span class="clefix"></span>
                        </p>
                        <p>
                            <a class="titlea" target="_blank" href="http://www.zcwz.com/new-_vipfuwu/">财富宝</a>
                            <a class="titlea" target="_blank" href="http://www.zcwz.com/new-_about/poster.asp?act=2">排名三甲</a>
                            <a class="titlea" target="_blank" href="http://www.zcwz.com/new-_about/poster.asp">广告服务</a>
                            <span class="clefix"></span>
                        </p>
                        <p class="botm">
                            <a class="title" target="_blank" href="http://www.zcwz.com/new-_about/contact.asp">帮助</a>
                            <a target="_blank" href="http://www.zcwz.com/new-_about/contact.asp">联系我们</a>
                        </p>
                    </div>
                </div>
            </li>
            <li class="lines"></li>
            <li><a href="http://www.zcwz.com/new-_about/contact.asp" target="_blank">帮助</a></li>
        </ul>
        <ul class="bar_left">
            <li><span style="margin-top:1px;">
            <strong>轴承型号查询</strong>
            </span></li>
            <li class="lines"></li>

            <li><a href="http://www.zcwz.com/fuwu.asp" target="_blank">请登录</a></li>
            <li class="lines"></li>
            <li><a href="http://www.zcwz.com/fuwu.asp?zhuce=next" target="_blank">免费注册</a></li>

        </ul>
        <ul class="clefix"></ul>
        <script type="text/javascript" language="JavaScript" src="http://www.zcwz.com/javascript/top_bar.js"></script>
    </div>
    <div class="clefix">&nbsp;</div>
</div>

<div class="head_logo"><h1><a href="/">轴承型号，轴承型号查询</a></h1></div>
<div id="content">
<div class="con_diva">
    <div class="con_datitle">
        <a class="a_codatb" href="../index.asp"><span>轴承型号查询</span></a>
        <a class="a_codata" href="../index1.asp"><span>国内外型号对照</span></a>
        <a class="a_codata" href="../index2.asp"><span>轴承样本</span></a>
        <a class="a_codata" href="http://www.zcwz.com/new-_zcpp/" target="_blank"><span>轴承品牌</span></a>		</div>
    <div class="con_dacon">
        <form action="index.asp?mode_x=1" method="post" name="c">
            <ul class="con_ula">
                <li><input type="radio" name="type_x" value="mh" checked="checked" />模糊搜索&nbsp;&nbsp;<input type="radio" name="type_x" value="jq" />精确搜索</li>
                <li class="con_lia"><input class="search_in" name="" type="submit" value="查询一下" /></li>
            </ul>
            <ul class="con_ulb">
                <li>型号：<input name="name_x" type="text" value="" />&nbsp;&nbsp;内径：<input name="bore_x" type="text" value="" />&nbsp;&nbsp;宽度：<input name="width_x" type="text" value="" />&nbsp;&nbsp;</li>
                <li>类型：<select name="class_x">
                        <option value="">任意类型</option>
                        <option value="1">深沟球轴承</option><option value="2">调心球轴承</option><option value="3">圆柱滚子轴承</option><option value="4">调心滚子轴承</option><option value="5">滚针轴承</option><option value="6">螺旋滚子轴承</option><option value="7">角接触球轴承</option><option value="8">圆锥滚子轴承</option><option value="9">推力球轴承</option><option value="10">推力角接触球轴承</option><option value="11">推力圆锥滚子轴承</option><option value="12">推力调心滚子轴承</option><option value="13">推力圆柱滚子轴承</option><option value="14">推力滚针轴承</option><option value="15">推力滚子轴承</option><option value="16">外球面轴承</option><option value="17">带座外球面轴承</option><option value="18">关节轴承</option><option value="19">万向节轴承</option><option value="20">转盘轴承</option><option value="21">单向轴承</option><option value="44">轧机轴承</option><option value="22">满装滚子轴承</option><option value="23">整体偏心轴承</option><option value="24">剖分轴承</option><option value="25">滚轮轴承</option><option value="26">组合轴承</option><option value="27">滚珠丝杠轴承</option><option value="28">薄壁轴承</option><option value="29">无油润滑轴承</option><option value="30">法兰轴承</option><option value="31">罗拉轴承</option><option value="32">直线运动轴承</option><option value="33">陶瓷轴承</option><option value="34">不锈钢轴承</option><option value="35">塑料轴承</option><option value="36">高温轴承</option><option value="37">增压器轴承</option><option value="38">水泵轴连轴承</option><option value="39">离合器轴承</option><option value="40">导轨</option><option value="41">轴承座</option><option value="42">非标轴承</option><option value="43">其它未知类型</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;外径：<input  name="ubore_x" type="text" value="" />&nbsp;&nbsp;重量：<input name="weight_x" type="text" value="" />&nbsp;&nbsp;</li>
            </ul>
            <ul class="clear">&nbsp;</ul>
        </form>
    </div>
</div>

<div style="text-align:center;margin-top:10px;" class="con_diva">
<div style="font-size:14px;text-align:left;text-indent:8px;">
    <b><a href="../index.asp">轴承型号查询></a><?=$prorow['xinxinghao'].$prorow['leixing']?></b></div>
<div class="xh_content">
<div class="xh_left">
<!--型号查询-->
<div id="xhcx">
    <div class="xh_leftTitle">
        <div class="xh_leftTitle_divL"><strong><?=$prorow['xinxinghao'].$prorow['leixing']?></strong></div>
        <div class="xh_leftTitle_divR"></div>
    </div>

    <div>

            <ul id="ul_err">
                <li class="li_err1" style="width:700px;">
                    <div class="div_err1_L">
                        <span class="subTitle">轴承类型：</span>
                        <a href="../index.asp?mode_x=1&classid_x=1" style="color:#444;"><?=$prorow['leixing']?></a>
                    </div>
                </li>
                <li class="clr"></li>
                <li class="li_err2t">
                    <div class="div_err2_L">
                        <span class="subTitle">国内新型号：</span>
                        <a href="../index.asp?mode_x=1&name_x=6300" style="color:#444;"><?=$prorow['xinxinghao']?></a>
                    </div>

                </li>

                <li class="li_err3t">
                    <div class="div_err3_L">
                        <span class="subTitle">国内旧型号：</span>
                        <a href="../index.asp?mode_x=1&name_x=300" style="color:#444;"><?=$prorow['jiuxinghao']?></a>
                    </div>

                </li>
                <li class="clr"></li>
                <li class="li_err1">
                    <div class="div_err1_L">
                        <span class="subTitle">内径(mm)：</span>
                        <a href="../index.asp?mode_x=1&bore_x=10" style="color:#444;"><?=$prorow['neijing']?></a>
                    </div>

                </li>

                <li class="li_err2">
                    <div class="div_err2_L">
                        <span class="subTitle">外径(mm)：</span>
                        <a href="../index.asp?mode_x=1&ubore_x=35" style="color:#444;"><?=$prorow['waijing']?></a>
                    </div>

                </li>

                <li class="li_err3">
                    <div class="div_err3_L">
                        <span class="subTitle">宽度(mm)：</span>
                        <a href="../index.asp?mode_x=1&width_x=11" style="color:#444;"><?=$prorow['kuandu']?></a>
                    </div>

                </li>
                <li class="clr"></li>
                <li class="li_err1">
                    <div class="div_err1_L">
                        <span class="subTitle">脂润滑转速(r/min)：</span><?=$prorow['zhisu']?>
                    </div>

                </li>

                <li class="li_err2">
                    <div class="div_err2_L">
                        <span class="subTitle">Cor(kN)：</span><?=$prorow['cor']?>
                    </div>

                </li>

                <li class="li_err3">
                    <div class="div_err3_L">
                        <span class="subTitle">Cr(kN)：</span><?=$prorow['cr']?>
                    </div>

                </li>
                <li class="clr"></li>
                <li class="li_err1">
                    <div class="div_err1_L">
                        <span class="subTitle">油润滑转速(r/min)：</span><?=$prorow['yiusu']?>
                    </div>

                </li>

                <li class="li_err2">
                    <div class="div_err2_L">
                        <span class="subTitle">重量(KG)：</span>
                        <a href="../index.asp?mode_x=1&weight_x=0.053" style="color:#444;"><?=$prorow['zhongliang']?></a>
                    </div>
                </li>
            </ul>
            <div class="clr"></div>


    </div>
</div>
<div class="clr"></div>

<!--型号对照-->
<div id="xhdz" style="margin-top:20px;">
    <div class="xh_leftTitle">
        <div class="xh_leftTitle_divL"><strong><?=$prorow['xinxinghao']?><?=$prorow['leixing']?>型号对照</strong></div>
        <div class="xh_leftTitle_divR"></div>
    </div>

    <div style="text-align:left;margin-left:30px;margin-top:10px;line-height:25px;" >
<?php
$pinpaisql=mysql_query("select opinpai,opingpaixinghao from pinpai where xinxinghao='6300'");
$pinpai=mysql_fetch_array($pinpaisql);
while ($info1 = mysql_fetch_array($sql))
{

?>
<span style='margin-right:25px;display:block;float:left;'>
<span class=subTitle><?=$info1["opinpai"]?></span>:
<a target=_blank href='../index1.asp?name_x=6300&brandid_x=11' style='color:#444;'><?=$info1["opingpaixinghao"]?></a>
</span>
<?php
}
?>
        <span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>RHP</span>:
            <a target=_blank href='../index1.asp?name_x=6300&brandid_x=11' style='color:#444;'>6300</a>
        </span>
        <span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>STEYR</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=12' style='color:#444;'>6300</a></span><span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>TORRINGTON,FAFNIR</span>:<a target=_blank href='../index1.asp?name_x=300K&brandid_x=0' style='color:#444;'>300K</a></span><span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>NSK</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=2' style='color:#444;'>6300</a></span>
        <span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>NACHI</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=6' style='color:#444;'>6300</a></span>
        <span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>NTN</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=3' style='color:#444;'>6300</a></span>
        <span style='margin-right:25px;display:block;float:left;'><span class=subTitle>KOYO</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=8' style='color:#444;'>6300</a></span>
        <span style='margin-right:25px;display:block;float:left;'><span class=subTitle>FAG</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=4' style='color:#444;'>6300</a></span>
        <span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>SKF</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=1' style='color:#444;'>6300</a>
        </span>
        <span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>MRC</span>:<a target=_blank href='../index1.asp?name_x=300S&brandid_x=24' style='color:#444;'>300S</a></span>
        <span style='margin-right:25px;display:block;float:left;'>
            <span class=subTitle>SNR</span>:<a target=_blank href='../index1.asp?name_x=6300&brandid_x=28' style='color:#444;'>6300</a>
        </span>
    </div>
</div>
<div class="clr"></div>

<!--轴承样本-->
<div id="zcyb" style="margin-top:20px;">
    <div class="xh_leftTitle">
        <div class="xh_leftTitle_divL"><strong><?=$prorow['xinxinghao']?><?=$prorow['leixing']?></strong></div>
        <div class="xh_leftTitle_divR"></div>
    </div>
    <div>

        <div class="yangben_img">
            <div class="imgDiv1"><a target="_blank" href="../index2.asp?class_x=1">
                    <img src="http://file.zcwz.com/img/images/200908/20090829141006.gif" align="middle" border="0" /></a></div>
            <div class="imgDiv2"><a target="_blank" href="../index2.asp?class_x=1&sclass_x=2">
                    <img src="http://file.zcwz.com/img/images/slt/0117.gif" align="middle" onload="resize_f(this,146,116,150,120)" border="0" /></a></div>
        </div>
        <table border="0" class="yangben_tab">
            <tr>
                <td height="20"><span class=subTitle>轴承类型：</span>
                    <a target="_blank" href="../index2.asp?class_x=1" style="color:#444;"><?=$prorow['leixing']?></a></td>
                <td><span class=subTitle>国内新型号：</span>
                    <a target="_blank" href="../index2.asp?mode_x=1&name_x=6300" style="color:#444;"><?=$prorow['xinxinghao']?></a></td>
                <td><span class=subTitle>规格(dxDxB)：</span>10×35×11</td>
            </tr>
            <tr>
                <td height="20"><span class=subTitle>子分类：</span>
                    <a target="_blank" href="../index2.asp?class_x=1&sclass_x=2" style="color:#444;">开式深沟球轴承</a></td>
                <td><span class=subTitle>国内旧型号：</span>
                    <a target="_blank" href="../index2.asp?mode_x=1&name_x=300" style="color:#444;"><?=$prorow['jiuxinghao']?></a></td>
                <td><span class=subTitle>重量(kg)：</span><?=$prorow['zhongliang']?></td>
            </tr>
        </table>

    </div>
</div>
<div class="clr"></div>

<!--类型描述-->
<div id="classDes">
    <div class="xh_leftTitle">
        <div class="xh_leftTitle_divL"><strong><?=$prorow['leixing']?></strong></div>
        <div class="xh_leftTitle_divR"></div>
    </div>
    <div style="width:95%;margin:10px auto;text-align:left;text-indent:25px;line-height:20px;">
<?php

$prosql1=mysql_query("select * from zcleixing where names='".$prorow['leixing']."'");

$prorow1=mysql_fetch_array($prosql1);
echo $prorow1["descrip"];
?>
       </div>
</div>
<div class="clr"></div>

</div>


<div class="xh_right">
    <div class="xh_rightTitle">
        6300相关型号
    </div>
    <ul class="xh_rightContent">
        <li><a href='show.asp?id=9005' target=_blank >61804-2RZ轴承</a></li><li><a href='show.asp?id=588' target=_blank >6014-Z轴承</a></li><li><a href='show.asp?id=2632' target=_blank >6005-2RS/Z3轴承</a></li><li><a href='show.asp?id=597' target=_blank >61916-Z轴承</a></li><li><a href='show.asp?id=7368' target=_blank >6307/Z2轴承</a></li><li><a href='show.asp?id=7355' target=_blank >6306-2RS/Z2轴承</a></li>
    </ul>

    <div class="xh_rightTitle" style="margin-top:8px;">
        6300相关供货
    </div>
    <ul class="xh_rightContent">
        <li><a href='http://www.zcwz.com/gonghuo/supply_2952601.html' target=_blank >970103轴承  轴承</a></li><li><a href='http://www.zcwz.com/gonghuo/supply_2991258.html' target=_blank >6004N</a></li><li><a href='http://www.zcwz.com/gonghuo/supply_3002160.html' target=_blank >61822-RS1</a></li><li><a href='http://www.zcwz.com/gonghuo/supply_2544126.html' target=_blank >6234-Z轴承，轴承尺寸，轴承价</a></li><li><a href='http://www.zcwz.com/gonghuo/supply_2544128.html' target=_blank >6234-2Z轴承，轴承尺寸，轴承</a></li><li><a href='http://www.zcwz.com/gonghuo/supply_2544130.html' target=_blank >6234-RS轴承，轴承尺寸，轴承</a></li>
    </ul>

    <div class="xh_rightTitle" style="margin-top:8px;">
        型号供应商
    </div>
    <ul class="xh_rightCompany">

        <li>
            <div ><a href='http://yttdzc.zcwz.com' target=_blank style="font-size:14px;font-weight:bold;" >油田特大轴承销售中心</a></div>
            <div style="text-indent:20px;margin-top:5px;line-height:18px;">本公司位于山东省临清市烟店——中国轴承城，京九铁路从这里经过......</div>
            <div style="color:#FF9900;margin-top:3px;"><b>主营：</b>深沟球轴承/ 调心球轴承...</div>
        </li>

        <li>
            <div ><a href='http://shanli.zcwz.com' target=_blank style="font-size:14px;font-weight:bold;" >东莞市山力机械轴承贸易有限公</a></div>
            <div style="text-indent:20px;margin-top:5px;line-height:18px;">东莞山力机械轴承贸易有限公司自2006年成立以来,已发展成为......</div>
            <div style="color:#FF9900;margin-top:3px;"><b>主营：</b>深沟球轴承/ 调心球轴承...</div>
        </li>


    </ul>

</div>

<div class="clr"></div>
</div>
</div>

</div>


<div id="algfoot">
    <div id="footer_a">
        <a href='http://www.zcwz.com/new-_about/about.asp' target='_blank'>关于我们</a>&nbsp;|&nbsp;
        <a href=# onclick=this.style.behavior='url(#default#homepage)';this.setHomePage('http://www.zcwz.com');>设为首页</a>&nbsp;|&nbsp;
        <a href=javascript:window.external.AddFavorite('http://www.zcwz.com','中华轴承网')>加入收藏</a>&nbsp;|&nbsp;
        <a href='http://www.zcwz.com/fuwu.asp?zhuce=service' target='_blank'>会员服务</a>&nbsp;|&nbsp;
        <a href='http://www.zcwz.com/new-_about/poster.asp' target='_blank'>广告服务</a>&nbsp;|&nbsp;
        <a href='http://www.zcwz.com/new-_about/contact.asp' target='_blank'>联系我们</a>&nbsp;|&nbsp;
        <a href='http://www.zcwz.com/new-_about/remittance.asp' target='_blank'>付款方式</a>&nbsp;|&nbsp;
        <a href='http://www.zcwz.com/new-_about/youqing_more.asp' target='_blank'>友情链接</a>
    </div>
    全国服务热线：400-711-0010&nbsp;&nbsp;&nbsp;&nbsp;在线客服12号：<a target="_blank" href=" http://wpa.qq.com/msgrd?v=1&uin=251173677&site=qq&menu=yes"><img border="0" src=" http://wpa.qq.com/pa?p=2:251173677:46" alt="点击这里给我发消息" title="点击这里给我发消息"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;在线客服15号：<a target="_blank" href=" http://wpa.qq.com/msgrd?v=1&uin=1041017633&site=qq&menu=yes"><img border="0" src=" http://wpa.qq.com/pa?p=2:1041017633:46" alt="点击这里给我发消息" title="点击这里给我发消息"></a><br />
    中华人民共和国增值电信业务经营许可证B2-20050102号&nbsp;&nbsp;<a href='http://www.miibeian.gov.cn' target='_blank'>浙ICP备09044943号</a>
    <div style="text-align:center;">
        <b style="color:#999;">中华轴承网-轴承型号查询最专业<a href="http://www.zcwz.com" style="color:#999;">轴承网</a></b>
    </div>




</body>
</html>

