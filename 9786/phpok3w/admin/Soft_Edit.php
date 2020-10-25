
<?php
Const dbdns="../"
?>
<?php require_once("chk.php");  ?>
<?php require_once "../AppCode/Class/Ok3w_Soft.php" ?>
<?php require_once("../AppCode/fun/function.php");  ?>
<?php require_once "../AppCode/fun/CreateHtml.php" ?>
<?php

Call CheckAdminFlag(6)

Set Soft = New Ok3w_Soft
action = Request.QueryString("action")
Id = Request.QueryString("Id")
ChannelID = Request.QueryString("ChannelID")
ClassID = Request.QueryString("ClassID")

action_ok = Request.Form("action_ok")
If action = "" Then action = "add"

Select Case action_ok
	Case "add"
		Call Soft.Add()
		Call SaveAdminLog("添加软件：" & Soft.SoftName)
		If Soft.IsPass=1 Then Call Soft_Page_Html(ID)
		Call ActionOk("Soft_Edit.php?ChannelID=" & ChannelID & "&ClassID=" & ClassID & "&action=" & action & "&ID=" & ID)
	Case "edit"
		Call Soft.Edit()
		Call SaveAdminLog("修改软件：Id=" & Soft.Id)
		If Soft.IsPass=1 Then Call Soft_Page_Html(ID)
		Call ActionOk("Soft_Edit.php?ChannelID=" & ChannelID & "&ClassID=" & ClassID & "&action=" & action & "&ID=" & ID)
End Select
If ClassID<>"" Then Soft.ClassID = Cdbl(ClassID)
If action="edit" Or action="copy" Then Call Soft.Load(Id)
If action="" Or action="copy" Then
	action = "add"
	Id = ""
End If

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>后台管理系统</title>
<link rel="stylesheet" type="text/css" href="images/Style.css">
<script language="javascript" src="../js/class.js"></script>
<script language="javascript" src="images/js.js"></script>
</head>

<body>
<?php require_once "top.php" ?>
<br />
<table cellspacing="0" cellpadding="0" width="98%" align="center" border="0">
  <tbody>
    <tr>
      <td style="PADDING-LEFT: 2px; HEIGHT: 22px" 
    background="images/tab_top_bg.gif"><table cellspacing="0" cellpadding="0" width="477" border="0">
        <tbody>
          <tr>
            <td width="147"><table height="22" cellspacing="0" cellpadding="0" border="0">
              <tbody>
                <tr>
                  <td width="3"><img id="tabImgLeft__0" height="22" 
                  src="images/tab_active_left.gif" width="3" /></td>
                  <td 
                background="images/tab_active_bg.gif" class="tab"><strong class="mtitle">软件编辑</strong></td>
                  <td width="3"><img id="tabImgRight__0" height="22" 
                  src="images/tab_active_right.gif" 
            width="3" /></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td bgcolor="#ffffff"><table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tbody>
          <tr>
            <td width="1" background="images/tab_bg.gif"><img height="1" 
            src="images/tab_bg.gif" width="1" /></td>
            <td 
          style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" 
          valign="top"><div id="tabContent__0" style="DISPLAY: block; VISIBILITY: visible">
              <table cellspacing="1" cellpadding="1" width="100%" align="center" 
            bgcolor="#8ccebd" border="0">
                <tbody>
                  <tr>
                    <td 
                style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" 
                valign="top" bgcolor="#fffcf7"><table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#EBEBEB">
                      <form id="Form" name="Form" method="post" action="?action=<?php
=action
?>&Id=<?php
=Id
?>&ChannelID=<?php
=ChannelID
?>">
                        
                        <tr bgcolor="#FFFFFF">
                          <td width="70" align="right">软件名称<span class="red">*</span></td>
                          <td><input name="SoftName" type="text" id="SoftName" value="<?php
=Soft.SoftName
?>" size="50" maxlength="255" />
						  标题颜色：
                            <input name="TitleColor" type="text" value="<?php
=Soft.TitleColor
?>" size="8" maxlength="7">
                            <select name="select" onChange="this.form.TitleColor.value=this.value;">
                              <option value="">选择</option>
                              <option value="#FF0000">红色</option>
                              <option value="#0000FF">蓝色</option>
                              <option value="#008800">绿色</option>
                            </select>                          </td>
                          </tr>
                        
                        <tr bgcolor="#FFFFFF">
                          <td align="right">跳转连接</td>
                          <td><input name="TitleURL" type="text" id="TitleURL" value="<?php
=Soft.TitleURL
?>" size="50" maxlength="255" />
                            点击标题直接打开的链接</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">软件版本</td>
                          <td><input name="Softver" type="text" id="Softver" value="<?php
=Soft.Softver
?>" size="10" maxlength="50">
                            大小
                            <input name="Softsize" type="text" id="Softsize" value="<?php
=Soft.Softsize
?>" size="8" />
                            <input name="Softsizeunit" type="radio" value="KB" <?php
If Soft.Softsizeunit="KB" Then
?>checked<?php
End If
?>>
KB
<input type="radio" name="Softsizeunit" value="MB" <?php
If Soft.Softsizeunit="MB" Then
?>checked<?php
End If
?>>
MB
<input type="radio" name="Softsizeunit" value="GB" <?php
If Soft.Softsizeunit="GB" Then
?>checked<?php
End If
?>>
GB</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">软件分类<span class="red">*</span></td>
                          <td><select name="ClassID"></select>
						   <script language="javascript">
						  InitSelect(document.Form.ClassID,"<?php
=ChannelId
?>","<?php
=Soft.ClassID
?>");
						  </script>
                            <input name="SortPath" type="hidden" id="SortPath" value="<?php
=Soft.SortPath
?>">
                            软件语言 <select name="Softlanguage">
                              <?php
Call InitSoftBaseChkItem(Soft.Softlanguage,"Softlanguage","select")
?>
                            </select>
                            软件授权
                            <select name="Softlicence">
                              <?php
Call InitSoftBaseChkItem(Soft.Softlicence,"Softlicence","select")
?>
                            </select>
                            软件属性 <select name="Softproperty">
                              <?php
Call InitSoftBaseChkItem(Soft.Softproperty,"Softproperty","select")
?>
                            </select>                            </td>
                        </tr>
                        
                        <tr bgcolor="#FFFFFF">
                          <td align="right">操作系统</td>
                          <td><?php
Call InitSoftBaseChkItem(Soft.SoftTos,"SoftTos","checkbox")
?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">推荐等级</td>
                          <td><input name="Softstar" type="radio" value="1" <?php
If Soft.Softstar=1 Then
?>checked<?php
End If
?>>★<input type="radio" name="Softstar" value="2" <?php
If Soft.Softstar=2 Then
?>checked<?php
End If
?>>★★<input type="radio" name="Softstar" value="3" <?php
If Soft.Softstar=3 Then
?>checked<?php
End If
?>>★★★<input type="radio" name="Softstar" value="4" <?php
If Soft.Softstar=4 Then
?>checked<?php
End If
?>>★★★★<input type="radio" name="Softstar" value="5" <?php
If Soft.Softstar=5 Then
?>checked<?php
End If
?>>★★★★★</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">软件作者</td>
                          <td><input name="Softauthor" type="text" id="Softauthor" value="<?php
=Soft.Softauthor
?>" size="10" />
                            作者主页
                              <input name="Softauthorurl" type="text" id="Softauthorurl" value="<?php
=Soft.Softauthorurl
?>" size="20" />
                              演示地址
                              <input name="Softdemourl" type="text" id="Softdemourl" value="<?php
=Soft.Softdemourl
?>" size="34" /></td>
                        </tr>
                        
                        <tr bgcolor="#FFFFFF">
                          <td align="right">软件介绍<span class="red">*</span></td>
                          <td>
						  <input name="Softintro" type="hidden" id="Softintro" value="<?php
=Server.HTMLEncode(Replace(Replace(Soft.Softintro,"upfiles/","../upfiles/"),"editor/","../editor/"))
?>" />
						  <IFRAME ID="eWebEditor1" SRC="../editor/ewebeditor.htm?id=Softintro&style=Ok3w&savepathfilename=UpFiles" FRAMEBORDER="0" SCROLLING="no" WIDTH="600" HEIGHT="350" style="border:1px solid #CCCCCC;"></IFRAME>
						  <input name="UpFiles" type="hidden" id="UpFiles">
						  <input name="eWebEditorUpFile" type="hidden" id="eWebEditorUpFile" value="1"></td>
                          </tr>
                        
                        <tr bgcolor="#FFFFFF">
                          <td align="right">软件状态</td>
                          <td><input name="IsPass" type="checkbox" id="IsPass" value="1" <?php
If Soft.IsPass Then
?>checked="checked"<?php
End If
?> />
通过审核

                            <input name="IsPlay" type="checkbox" id="IsPlay" value="1" <?php
If Soft.IsPlay Then
?>checked="checked"<?php
End If
?> />
                            首页轮播
<input name="IsMove" type="checkbox" id="IsMove" value="1" <?php
If Soft.IsMove Then
?>checked="checked"<?php
End If
?> />
滚动
<input name="IsCommend" type="checkbox" id="IsCommend" value="1" <?php
If Soft.IsCommend Then
?>checked="checked"<?php
End If
?> />推荐
<input name="IsTop" type="checkbox" id="IsTop" value="1" <?php
If Soft.IsTop Then
?>checked="checked"<?php
End If
?> />
                            置顶                            
                            <input name="IsDelete" type="hidden" id="IsDelete" value="<?php
=Soft.IsDelete
?>"></td>
                          </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">&nbsp;软件图片</td>
                          <td><input name="Softimageurl" type="text" id="Softimageurl" size="50" value="<?php
=Soft.Softimageurl
?>" />
                            <input type="button" name="Submit2" value="选择图片" onClick="Get_eWebEditor_Img();" style="border:1px solid #CCCCCC; background-color:#FFFFFF;">
							<style type="text/css">
							#eImg{border:1px solid #CCCCCC; padding:5px; margin:5px; display:none;}
							#eImg img{border:1px solid #666666; cursor:pointer; width:100px; height:80px;}
							</style>
							<div id="eImg"></div>
                            <iframe scrolling="no" frameborder="0" width="100%" height="100" src="../editor/upload_files.php?formID=Form&objID=Softimageurl"></iframe></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">下载地址<span class="red">*</span></td>
                          <td><input name="Softdownloadurl" type="text" value="<?php
=Soft.Softdownloadurl
?>" size="50">
                            <input type="button" name="Submit3" value="上传文件" onClick="ShowUpLoadFile(this.form.Softdownloadurl)" style="border:1px solid #CCCCCC; background-color:#FFFFFF;">
							<div style="margin-top:5px;" class="red">注意：如果文件比较大，请用FTP上传，再把地址复制过来；如果直接引用其它网站资源，必须以http://开头。</div></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">下载权限</td>
                          <td><select name="vUserGroupID" id="vUserGroupID">
                            <option value="0">==游客==</option>
							<?php
Call InitUserDengjiSelectOption(Soft.vUserGroupID)
?>
                          </select>
                            <input name="vUserMore" type="checkbox" id="vUserMore" value="1" <?php
If Soft.vUserMore=1 Then
?>checked<?php
End If
?>>
                            及以上级别，或是指定最低积分：
                            <input name="vUserJifen" type="text" id="vUserJifen" size="4" value="<?php
=Soft.vUserJifen
?>">                             </td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                          <td align="right">添加时间</td>
                          <td><input name="Updatetime" type="text" id="Updatetime" value="<?php
=Soft.Updatetime
?>" size="20" />
                            下载次数
                              <input name="Hits" type="text" id="Hits" value="<?php
=Soft.Hits
?>" size="4" />
                              项次数
                              <input name="Ding_Hits" type="text" id="Ding_Hits" value="<?php
=Soft.Ding_Hits
?>" size="4" />
                              踩次数
                              <input name="Cai_Hits" type="text" id="Cai_Hits" value="<?php
=Soft.Cai_Hits
?>" size="4" /></td>
                        </tr>
                        
                        <tr bgcolor="#FFFFFF">
                          <td align="right">&nbsp;</td>
                          <td><input name="action_ok" type="hidden" id="action_ok" value="<?php
=action
?>" />
                            <input name="IsUserAdd" type="hidden" id="IsUserAdd" value="<?php
=Soft.IsUserAdd
?>">
                            <input name="GiveJifen" type="hidden" id="GiveJifen" value="<?php
=Soft.GiveJifen
?>">
                            <input name="Inputer" type="hidden" id="Inputer" value="<?php
=Soft.Inputer
?>">
                            <input name="bntSubmit" type="button" id="bntSubmit" onClick="submitform(forms[0]);" value="保 存"/>
                                <input type="button" name="Submit" value="取 消" onClick="javascript:document.URL='Soft_List.php?ChannelID=<?php
=ChannelID
?>';" /></td>
                          </tr>
                      </form>
                    </table>
<script language="JavaScript" type="text/javascript">
function submitform(frm)
{
	if(frm.SoftName.value.trim()=="")
	{
		ShowErrMsg("软件名称不能为空，请输入");
		frm.SoftName.focus();
		return false;
	}
	if(eWebEditor1.eWebEditor.document.body.innerHTML.trim()=="")
	{
		ShowErrMsg("软件简介不能为空，请输入");
		eWebEditor1.eWebEditor.focus();
		return false;
	}
	if(frm.Softdownloadurl.value.trim()=="")
	{
		ShowErrMsg("下载地址不能为空，请输入");
		frm.Softdownloadurl.focus();
		return false;
	}
	
	frm.action = frm.action + "&ClassID=" + frm.ClassID.value;
	
	frm.bntSubmit.disabled = true;
	frm.bntSubmit.value = "请稍候...";
	
	frm.submit();
}

function ShowUpLoadFile(obj)
{
	var upUrl = "../download/asp/upload_files.php";
	var values = showModalDialog(upUrl,self,"dialogWidth:350px;dialogHeight:150px;help:no;scroll:auto;status:no");
	if(values!=undefined)
	{
		var arr = values.split("|")
		var fname = arr[0];
		var fsize = arr[1];
		
		obj.value = fname;
		obj.form.Softsize.value = fsize;
	}
}

function Get_eWebEditor_Img()
{	
	var imgs = eWebEditor1.eWebEditor.document.getElementsByTagName('img');
	var imgstr = "";
	for(var img=0;img<imgs.length;img++)
	{
		imgstr = imgstr + '<img onclick="Set_Img(this.src)" src="'+imgs[img].src+'" /> ';
	}
	if(imgstr=="")
		imgstr = "编辑器没有任何可用图片。";
	eImg.innerHTML = imgstr;
	eImg.style.display = "block";
}
function Set_Img(src)
{
	var sPath = document.location.host + document.location.pathname;
	sPath = sPath.substr(0, sPath.length-13);
	var tmp = sPath.split("/");
	var url = "http://";
	for(var i=0;i<tmp.length-2;i++)
		url = url + tmp[i] + "/";
	Form.Softimageurl.value = src.replace(url,"");
	eImg.style.display = "none";
}
</script>
</td>
                  </tr>
                </tbody>
              </table>
            </div></td>
            <td width="1" background="images/tab_bg.gif"><img height="1" 
            src="images/tab_bg.gif" width="1" /></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
    <tr>
      <td background="images/tab_bg.gif" bgcolor="#ffffff"><img height="1" 
      src="images/tab_bg.gif" width="1" /></td>
    </tr>
  </tbody>
</table>
</body>
</html>
<?php

Call CloseConn()
Set Soft = Nothing
Set Admin = Nothing

?>

