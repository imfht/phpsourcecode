<?php
defined('IN_SYSTEM') or exit('Access Denied');
show_menu($menus);
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <title>后台管理系统</title>
    <link rel="stylesheet" type="text/css" href="images/Style.css">
    <script language="javascript">
        function ChkAll()
        {
            var obj = document.form2.IdList;
            for(var i=0;i<obj.length;i++)
                obj[i].checked = !obj[i].checked;
        }
    </script>
</head>

<body>
<?php require_once "top.php" ?>
<br />
<table cellspacing="0" cellpadding="0" width="98%" align="center" border="0">
    <tbody>
    <tr>
        <td style="PADDING-LEFT: 2px; HEIGHT: 22px"  background="images/tab_top_bg.gif">
            <table   width="477"  >
                <tbody>
                <tr>
                    <td width="147">
                        <table height="22" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                            <tr>
                                <td width="3">
                                    <img id="tabImgLeft__0" height="22" src="images/tab_active_left.gif" width="3"/></td>
                                <td class="mtitle" background="images/tab_active_bg.gif">留言/评论管理</td>
                                <td width="3">
                                    <img id="tabImgRight__0" height="22" src="images/tab_active_right.gif" width="3"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff">


            <table cellspacing="0" cellpadding="0" width="100%" border="0">
                <tbody>
                <tr>
                    <td width="1" background="images/tab_bg.gif"><img height="1"  src="images/tab_bg.gif" width="1" /></td>
                    <td  style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" valign="top">
                        <div id="tabContent__0" style="DISPLAY: block; VISIBILITY: visible">

                            <table cellspacing="1" cellpadding="1" width="100%" align="center" bgcolor="#8ccebd" border="0">
                                <tbody>
                                <tr>
                                    <td style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px"  valign="top" bgcolor="#fffcf7">

                                        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC" class="gridtable">
                                            <form id="form1" name="form1" method="get" action="">
                                                <tr>
                                                    <td height="30" colspan="10" align="left" bgcolor="#EBEBEB">&nbsp;
                                                        <select name="stype" id="stype">
                                                            <option value="UserName">按用户</option>
                                                            <option value="Title">按标题</option>
                                                            <option value="Content">按内容</option>
                                                        </select>
                                                        <input name="keyword" type="text" id="keyword"/>
                                                        <input type="submit" name="Submit2" value="搜索"/></td>
                                                </tr>
                                            </form>
                                            <tr>
                                                <th height="25" align="center" bgcolor="#EBEBEB">用户名</th>
                                                <th align="center" bgcolor="#EBEBEB">电话</th>
                                                <th align="center" bgcolor="#EBEBEB">始发地</th>
                                                <th align="center" bgcolor="#EBEBEB">目的地</th>
                                                <th align="center" bgcolor="#EBEBEB">备注</th>
                                                <th align="center" bgcolor="#EBEBEB">添加日期</th>
                                                <th align="center" bgcolor="#EBEBEB">添加IP</th>
                                                <th align="center" bgcolor="#EBEBEB">状态</th>
                                                <th align="center" bgcolor="#EBEBEB">选择<br>
                                                    <input type="checkbox" onClick="ChkAll()"></th>
                                            </tr>
<form id="form2" name="form2" method="post" action="?ispass=<?=$ispass?>&page=<?=$CurrentPage?>">
    <?php foreach($lists as $k=>$row) {?>
        <tr>
            <td height="25" align="center" bgcolor="#FFFFFF"><?=htmlspecialchars_decode($row["username"])?></td>
            <td><?= $row["telephone"] ?></td>
            <td><?= $row["origin"] ?></td>
            <td><?= $row["target"] ?></td>
            <td><?=htmlspecialchars_decode($row["remark"]) ?></td>
            <td><?= $row["addtime"] ?></td>
            <td><?= $row["ip"] ?></td>
            <td align="center" bgcolor="#FFFFFF">
                <?php if ($row["istop"] == 1)
                { ?><font color="#0000FF">置顶</font>&nbsp;<? } ?>
                <?php if ($row["isdelete"] == 1)
                {
                    echo "删除";
                } else
                {
                    echo "正常";
                } ?>
                <a href="#" onClick="g_show(<?=$row["id"]; ?>)">
                    <?php if($row["ispass"]!=1)
                    {
                        ?><font color="#FF0000">未查看</font><?
                    } else
                    {
                        ?>已查看<? } ?></a></td>
            <td align="center" bgcolor="#FFFFFF">
                <input name="IdList[]" type="checkbox" id="IdList" value="<?=$row["id"] ?>">
            </td>
        </tr>
    <?php }?>


    <tr>
        <td height="25" colspan="10" align="right" bgcolor="#FFFFFF">
            <input name="cmd" type="submit" id="Cmd" value="已读">
            <input name="cmd" type="submit" id="Cmd" value="未读">
            <input name="cmd" type="submit" id="Cmd" value="置顶">
            <input name="cmd" type="submit" id="Cmd" value="取消置顶">
            <input name="cmd" type="submit" id="Cmd" onClick="if(!confirm('真的要删除吗？')) return false;" value="删除"></td>
    </tr>
</form>
                                            <tr><td height="25" colspan="10" bgcolor="#FFFFFF"><?=$pageStr;?></td></tr>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div></td>
                    <td width="1" background="images/tab_bg.gif">
                        <img height="1"  src="images/tab_bg.gif" width="1" /></td>
                </tr>
                </tbody>
            </table></td>
    </tr>
    <tr>
        <td background="images/tab_bg.gif" bgcolor="#ffffff">
            <img height="1"  src="images/tab_bg.gif" width="1" /></td>
    </tr>
    </tbody>
</table>


<div id="g_edit" style="z-index:9999; position:absolute; top:85px; left:20px; display:none;">
    <iframe scrolling="auto" id="g_url" name="g_url" width="500" height="400" frameborder="1"></iframe>
    <script language="javascript">
        function g_show(id)
        {
            // var url="Guest_Edit.php?id="+id+"&TypeID=100";
            // window.open (url,'newwindow','height=100,width=400,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no')
            document.getElementById("g_edit").style.display = "";
            g_url.location.href = "Guest_Edit.php?id="+id+"&TypeID=100";
        }
        function g_hidde()
        {
            document.getElementById("g_edit").style.display = "none";
        }
    </script>
</div>
</body>
</html>