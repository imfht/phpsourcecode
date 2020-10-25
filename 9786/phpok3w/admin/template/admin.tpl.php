<?php
defined('IN_SYSTEM') or exit('Access Denied');
show_menu($menus);
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <title>后台管理系统</title>
    <link rel="stylesheet" type="text/css" href="images/Style.css">
</head>

<body>
<?php require_once "top.php" ?>
<br />
<table cellspacing="0" cellpadding="0" width="98%" align="center" border="0">
    <tbody>
    <tr>
        <td style="PADDING-LEFT: 2px; HEIGHT: 22px"  background="images/tab_top_bg.gif">
            <table cellspacing="0" cellpadding="0" width="477" border="0">
                <tbody>
                <tr>
                    <td width="147">
                        <table height="22" cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                            <tr>
                                <td width="3">
                                    <img id="tabImgLeft__0" height="22"  src="images/tab_active_left.gif" width="3" /></td>
                                <td class="mtitle"  background="images/tab_active_bg.gif">管理员管理</td>
                                <td width="3">
                                    <img id="tabImgRight__0" height="22"   src="images/tab_active_right.gif"  width="3" /></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table></td>
    </tr>
    <tr>
        <td bgcolor="#ffffff">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
                <tbody>
                <tr>
                    <td width="1" background="images/tab_bg.gif">
                        <img height="1" src="images/tab_bg.gif" width="1" /></td>
                    <td style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" valign="top">
                        <div id="tabContent__0" style="DISPLAY: block; VISIBILITY: visible">
                            <table cellspacing="1" cellpadding="1" width="100%" align="center" bgcolor="#8ccebd" border="0">
                                <tbody>
                                <tr>
                                    <td style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px"
                                        valign="top" bgcolor="#fffcf7">
<table border="0" cellpadding="5" cellspacing="1" bgcolor="#CCCCCC">
    <form action="?" method="post">
        <tr>
            <td align="center" bgcolor="#EBEBEB">用户名</td>
            <td align="center" bgcolor="#EBEBEB">密码</td>
            <td align="center" bgcolor="#EBEBEB">权限</td>
            <td align="center" bgcolor="#EBEBEB">禁止登陆</td>
            <td align="center" bgcolor="#EBEBEB">操作</td>
        </tr>


  <tr>
      <td align="center" bgcolor="#FFFFFF">
          <input name="AdminName" type="text" id="AdminName" value=""/></td>
      <td align="center" bgcolor="#FFFFFF"><input name="AdminPwd" type="password" id="AdminPwd" /></td>
      <td bgcolor="#FFFFFF"><input name="GroupId" type="checkbox" id="GroupId" value="1"> 系统设置
          <input name="GroupId" type="checkbox" id="GroupId" value="2">网站说明<br>
          <input name="GroupId" type="checkbox" id="GroupId" value="3">新闻编辑
          <input name="GroupId" type="checkbox" id="GroupId" value="4">留言管理<br>
          <input name="GroupId" type="checkbox" id="GroupId" value="5">会员管理
          <input name="GroupId" type="checkbox" id="GroupId" value="6">软件管理
      </td>
      <td align="center" bgcolor="#FFFFFF">
          <input name="AdminLock" type="radio" value="0" checked="checked" />否
          <input name="AdminLock" type="radio" value="1" />是
      </td>
      <td align="center" bgcolor="#FFFFFF">
          <input type="button" name="Submit" value=" 添 加 " onClick="javascript:formsubmit(this.form,'add');" />
          <input name="action" type="hidden" id="action" />
      </td>
  </tr>




    </form>
</table>
<br />


<table border="0" cellpadding="5" cellspacing="1" bgcolor="#CCCCCC">
    <tr>
        <td align="center" bgcolor="#EBEBEB">用户名</td>
        <td align="center" bgcolor="#EBEBEB">密码</td>
        <td align="center" bgcolor="#EBEBEB">权限</td>
        <td align="center" bgcolor="#EBEBEB">锁定</td>
        <td align="center" bgcolor="#EBEBEB">操作</td>
    </tr>

    <form id="form1" name="form1" method="post" action="">

        <?php
        var_dump($lists);
        foreach($lists as $k=>$v)
        {
        ?>
        <tr>
            <td align="center" bgcolor="#FFFFFF">
                <input name="AdminName" type="text" id="AdminName" value="<?php echo $v['adminname'];?>" readonly="readonly" />
            </td>
            <td align="center" bgcolor="#FFFFFF">
                <input name="AdminPwd" type="password" id="AdminPwd" />
            </td>
            <td bgcolor="#FFFFFF">
                <input name="GroupId" type="checkbox" id="GroupId" value="1"  checked >系统设置
                <input name="GroupId" type="checkbox" id="GroupId" value="2"  checked />网站说明<br>
                <input name="GroupId" type="checkbox" id="GroupId" value="3" checked />新闻编辑
                <input name="GroupId" type="checkbox" id="GroupId" value="4"  checked />留言管理<br>
                <input name="GroupId" type="checkbox" id="GroupId" value="5" checked />会员管理
                <input name="GroupId" type="checkbox" id="GroupId" value="6" checked />软件管理
            </td>
            <td align="center" bgcolor="#FFFFFF">
                <input name="AdminLock" type="radio" value="0" checked /> 否
                <input name="AdminLock" type="radio" value="1" checked /> 是
            </td>
            <td align="center" bgcolor="#FFFFFF"><input type="button" name="Submit4" value="修改" onClick="javascript:formsubmit(this.form,'edit');"  />
                <input type="button" name="Submit5" value="删除" onClick="javascript:if(!confirm('真的要删除吗？')) return false;formsubmit(this.form,'del');"  disabled="disabled"  />
                <input name="AdminId" type="hidden" id="AdminId" value="" />
                <input name="action" type="hidden" id="action" />
            </td>
        </tr>

        <?
        }
        ?>
    </form>
</table>
<script language="JavaScript" type="text/javascript">
    function formsubmit(frm,action)
    {
        if(frm.AdminName.value.trim()=="")
        {
            ShowErrMsg("管理员名称不能为空，请输入");
            frm.AdminName.focus();
            return false;
        }
        if(frm.AdminPwd.value.trim()=="" && action =="add")
        {
            ShowErrMsg("管理员密码不能为空，请输入");
            frm.AdminPwd.focus();
            return false;
        }

        frm.action.value = action;
        frm.submit();
    }
</script>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div></td>
                    <td width="1" background="images/tab_bg.gif">
                        <img height="1" src="images/tab_bg.gif" width="1" /></td>
                </tr>
                </tbody>
            </table></td>
    </tr>
    <tr>
        <td background="images/tab_bg.gif" bgcolor="#ffffff">
            <img height="1" src="images/tab_bg.gif" width="1" /></td>
    </tr>
    </tbody>
</table>
</body>
</html>