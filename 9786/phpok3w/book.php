<?php
$Base_Target = "";
$ChannelID = 1;
require_once "AppCode/Conn.php";
require_once "AppCode/fun/function.php";
require_once "AppCode/Pager.php";
require_once "vbs.php";

$keyword=CmdSafeLikeSqlStr($_GET["keyword"]);
$ClassID = "";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <title>留言交流 - 我的网站</title>
    <script language="javascript" src="js/js.js"></script>
    <script language="javascript" src="js/ajax.js"></script>
    <link rel="stylesheet" type="text/css" href="images/default/style.css">
</head>

<body>
<?php include "head.php" ?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="nav">
    <tr>
        <td><strong>当前位置：</strong><a href="./">网站首页</a> &gt;&gt; 留言交流 </td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="MainTable">
    <tr>
        <td style="padding:8px; border:1px solid #CCC;">

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="150" align="center">共有<strong>1</strong>篇留言</td>
                    <td><div class="page_nav"><a href="book.php?PageNo=1">First</a> <a href="book.php?PageNo=1">Previous</a> <a href="book.php?PageNo=1" style="font-weight:bold; color:#FF0000;">1</a> <a href="book.php?PageNo=1">Next</a> <a href="book.php?PageNo=1">Last</a></div></td>
                </tr>
            </table>
            <div class="ly_gg">最新公告：XXX</div>

            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="ly_bb" style="table-layout: fixed; word-wrap:break-word;">
                <tr>
                    <td width="150" align="center" valign="top"><img src="images/book/5.jpg" class="ly_bhead" /><br />
                        Ok3w.Net</td>
                    <td valign="top">
                        <div class="ly_rr"><span>1</span>楼</div>
                        <div class="ly_cc">Ok3w.Net
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><div class="ly_ll">2010/1/27 16:22:39</div></td>
                </tr>
            </table>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="150" align="center">共有<strong>1</strong>篇留言</td>
                    <td><div class="page_nav"><a href="book.php?PageNo=1">First</a> <a href="book.php?PageNo=1">Previous</a> <a href="book.php?PageNo=1" style="font-weight:bold; color:#FF0000;">1</a> <a href="book.php?PageNo=1">Next</a> <a href="book.php?PageNo=1">Last</a></div></td>
                </tr>
            </table>

            <a name="sub" id="sub"></a>
            <div class="tit">
                <div class="tit_b"><strong>发表留言</strong></div>
                <div class="tit_c"><div class="zoom">
                        <form name="frmBook" id="frmBook" method="post" action="">
                            <table border="0" cellspacing="0" cellpadding="5">

                                <tr>
                                    <td><table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>姓名<span class="red12">*</span><br />
                                                    <input name="UserName" type="text" id="UserName" size="12" maxlength="8" value="" /></td>
                                                <td>&nbsp;</td>
                                                <td>联系QQ<br />
                                                    <input name="QQ" type="text" id="QQ" size="12" maxlength="20" /></td>
                                                <td>&nbsp;</td>
                                                <td>邮箱 <br />
                                                    <input name="Mail" type="text" id="Mail" size="25" maxlength="100" /></td>
                                                <td>&nbsp;</td>
                                                <td>个人主页 <br />
                                                    <input name="Homepage" type="text" id="Homepage" size="25" maxlength="100" /></td>
                                            </tr>
                                        </table></td>
                                    <td rowspan="4" valign="top">
                                        <div class="ly_head">
                                            
                                            <div>选择一个你喜欢的头像</div>
                                            <input name="pID" type="radio" value="1" checked="checked" /><a href="javascript:;" onclick="frmBook.pID[0].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/1.jpg" width="55" height="55"  border="0" /></a>
                                            <input name="pID" type="radio" value="2" /><a href="javascript:;" onclick="frmBook.pID[1].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/2.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="3" /><a href="javascript:;" onclick="frmBook.pID[2].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/3.jpg" width="55" height="55"  border="0" /></a>

                                            <input name="pID" type="radio" value="4" /><a href="javascript:;" onclick="frmBook.pID[3].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/4.jpg" width="55" height="55"  border="0" /></a> <br /><input name="pID" type="radio" value="5" /><a href="javascript:;" onclick="frmBook.pID[4].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/5.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="6" /><a href="javascript:;" onclick="frmBook.pID[5].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/6.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="7" /><a href="javascript:;" onclick="frmBook.pID[6].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/7.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="8" /><a href="javascript:;" onclick="frmBook.pID[7].checked=true;">
                                                <img alt="Ok3w.Net -- 点击选择" src="images/book/8.jpg" width="55" height="55"  border="0" /></a> <br /><input name="pID" type="radio" value="9" /><a href="javascript:;" onclick="frmBook.pID[8].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/9.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="10" /><a href="javascript:;" onclick="frmBook.pID[9].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/10.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="11" /><a href="javascript:;" onclick="frmBook.pID[10].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/11.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="12" /><a href="javascript:;" onclick="frmBook.pID[11].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/12.jpg" width="55" height="55"  border="0" /></a> <br /><input name="pID" type="radio" value="13" /><a href="javascript:;" onclick="frmBook.pID[12].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/13.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="14" /><a href="javascript:;" onclick="frmBook.pID[13].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/14.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="15" /><a href="javascript:;" onclick="frmBook.pID[14].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/15.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="16" /><a href="javascript:;" onclick="frmBook.pID[15].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/16.jpg" width="55" height="55"  border="0" /></a> <br /><input name="pID" type="radio" value="17" /><a href="javascript:;" onclick="frmBook.pID[16].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/17.jpg" width="55" height="55"  border="0" /></a> <input name="pID" type="radio" value="18" /><a href="javascript:;" onclick="frmBook.pID[17].checked=true;"><img alt="Ok3w.Net -- 点击选择" src="images/book/18.jpg" width="55" height="55"  border="0" /></a>
                                        </div></td>
                                </tr>
                                <tr>
                                    <td>留言<span class="red12">*</span> <br />
                                        <script language="javascript" src="js/guest_ubb.js"></script>
                                        <table border="0" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td><select name="font" onFocus="this.selectedIndex=0" onChange="chfont(this.options[this.selectedIndex].value)" size="1">
                                                        <option value="" selected>选择字体</option>
                                                        <option value="宋体">宋体</option>
                                                        <option value="黑体">黑体</option>
                                                        <option value="Arial">Arial</option>
                                                        <option value="Book Antiqua">Book Antiqua</option>
                                                        <option value="Century Gothic">Century Gothic</option>
                                                        <option value="Courier New">Courier New</option>
                                                        <option value="Georgia">Georgia</option>
                                                        <option value="Impact">Impact</option>
                                                        <option value="Tahoma">Tahoma</option>
                                                        <option value="Times New Roman">Times New Roman</option>
                                                        <option value="Verdana">Verdana</option>
                                                    </select></td>
                                                <td><select name="size" onFocus="this.selectedIndex=0" onChange="chsize(this.options[this.selectedIndex].value)" size="1">
                                                        <option value="" selected>字体大小</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                    </select></td>
                                                <td><select name="color"  onFocus="this.selectedIndex=0" onChange="chcolor(this.options[this.selectedIndex].value)" size="1">
                                                        <option value="" selected>字体颜色</option>
                                                        <option value="Black" style="background-color:black;color:black;">Black</option>
                                                        <option value="White" style="background-color:white;color:white;">White</option>
                                                        <option value="Red" style="background-color:red;color:red;">Red</option>
                                                        <option value="Yellow" style="background-color:yellow;color:yellow;">Yellow</option>
                                                        <option value="Pink" style="background-color:pink;color:pink;">Pink</option>
                                                        <option value="Green" style="background-color:green;color:green;">Green</option>
                                                        <option value="Orange" style="background-color:orange;color:orange;">Orange</option>
                                                        <option value="Purple" style="background-color:purple;color:purple;">Purple</option>
                                                        <option value="Blue" style="background-color:blue;color:blue;">Blue</option>
                                                        <option value="Beige" style="background-color:beige;color:beige;">Beige</option>
                                                        <option value="Brown" style="background-color:brown;color:brown;">Brown</option>
                                                        <option value="Teal" style="background-color:teal;color:teal;">Teal</option>
                                                        <option value="Navy" style="background-color:navy;color:navy;">Navy</option>
                                                        <option value="Maroon" style="background-color:maroon;color:maroon;">Maroon</option>
                                                        <option value="LimeGreen" style="background-color:limegreen;color:limegreen;">LimeGreen</option>
                                                    </select></td>
                                                <td><img src="images/bb_bold.gif" border="0" alt="粗体" onClick="ubbFormat('B')"></td>
                                                <td><img src="images/bb_italicize.gif" alt="斜体" width="23" height="22" border="0" onClick="ubbFormat('I')"></td>
                                                <td><img src="images/bb_underline.gif" border="0" alt="下划线" onClick="ubbFormat('U')"></td>
                                                <td><img src="images/bb_center.gif" border="0" alt="居中对齐" onClick="ubbFormat('CENTER')"></td>
                                                <td><img src="images/bb_email.gif" border="0" alt="插入EMAIL地址" onClick="ubbFormat('EMAIL')"></td>
                                                <td><img src="images/bb_url.gif" border="0" alt="插入网址" onClick="ubbFormat('URL')"></td>
                                                <td><img src="images/bb_image.gif" border="0" alt="插入图片" onClick="ubbInsert('IMG')"></td>
                                            </tr>
                                        </table>
                                        <textarea name="Content" cols="78" rows="15" id="Content"></textarea></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>验证：<span class="red12">*</span> → </td>
                                                <td class="vcode"><img src="./c/validcode.php" alt="看不清？点击换一个" name="strValidCode" width="40" height="10" border="0" id="strValidCode" onclick="Get_ValidCode('./');"/></td>
                                            </tr>
                                        </table>
                                        <input name="ValidCode" type="text" id="ValidCode" size="6" maxlength="4" /></td>
                                </tr>
                                <tr>
                                    <td><input name="bntSubmit" type="button" class="bbnt" onclick="Ok3w_Book_Save(this.form,'./',100,0);" value="OK!立即发表" style="margin-top:15px; padding-top:5px; cursor:pointer;" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div></td>
    </tr>
</table>
<?php include "foot.php" ?>
</body>
</html>
