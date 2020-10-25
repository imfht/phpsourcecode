<?php
	$strCommonTitle = 'Whoneed智能系统';
	$strCommonUrl	= 'http://www.whoneed.com';
    if( !Yii::app()->user->getName() ){
        header('HTTP/1.1 301 Moved Permanently');  
        header('Location: /admin_login.php');
    }
?>
<html><head><title><?php echo $strCommonTitle;?> -- 管理后台</title>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<link href="/static/admin/css/style.css" type=text/css rel=stylesheet>
<script src="/admin/js/dwz/js/jquery-1.7.1.js" type="text/javascript"></script>
</head>
<body leftMargin=0 topMargin=0 marginwidth="1" marginheight="0"><!--Start Top-->
<table cellSpacing=0 cellPadding=0 width="100%" bgColor=#003399 border=0>
  <tbody>
  <tr height=40>
    <td vAlign=center><a href="<?php echo $strCommonUrl;?>">&nbsp;</a></td>
    <td class=maindesc align=right width=400><a class=topbar 
      href="<?php echo $strCommonUrl;?>">Contact us with any 
      questions</a>&nbsp;&nbsp; </td></tr></tbody></table><br><br><span 
class=windowheader>
<CENTER><a href="<?php echo $strCommonUrl;?>"><b><?php echo $strCommonTitle;?> -- 管理后台</b></a></CENTER></span><br><br>
 
<CENTER><FONT face="Verdana, Arial" color=red size=1><B></B></FONT> 
<form id="changePassForm" method=post action='/admin/site/weakPassChange'>
<table cellSpacing=0 cellPadding=0 width=344 align=center bgColor=#cccccc 
border=0>
  <tbody>
    <tr><td valign="top"><img height=2 width=1><table cellSpacing=0 cellPadding=0 width=330 align=center bgColor=#f5f5f5 border=0></tr>
  <tbody>
        <tr>
          <td></td>
        </tr>
        <tr>
          <td class=maindesc align=middle><br><font color="red">你的密码为纯数字组成的,过于简单,必须修改才能登录!</font></a>. <br><br>
            <table cellSpacing=0 cellPadding=4 width=250 align=center 
            bgColor=#f5f5f5 border=0>
              <tbody>
                  <tr>
                    <td class=maindescbig>原密码</td>
                    <td align=left><input type="password" name="old_pass" size=18></td>
                  </tr>
                  <tr>
                    <td class=maindescbig>新密码</td>
                    <td align=left><input type="password" name="new_pass" size=18></td>
                  </tr>
                  <tr>
                    <td class=maindescbig>重复密码</td>
                    <td align=left><input type="password" name="new_pass_agin" size=18></td>
                  </tr>				  
                  <tr>
                    <td><input type="submit" value="提交"></td>
                    <td><input type="reset" value="取消"></td>
                  </tr>
              </tbody>
            </table>
          </td>
        </tr>
  </tbody>
</table><img width=1 height=2></a></td></tr></tbody></table></form>
<br><br>
<div class=maindesc align=center>Powered by <b><a href="<?php echo $strCommonUrl;?>" title="<?php echo $strCommonTitle;?>" target="_blank"><span style="color: #FF9900"><?php echo $strCommonTitle;?></span></a></b> Copyright &copy; <?php echo date('Y', time()); ?> All Rights Reserved .</div></td></tr></table><br></CENTER></body></html>
<script>
    $("#changePassForm").submit(function(){
        if( checkPass() ){
            $.post("/admin/site/weakPassChange",$("#changePassForm").serialize(),function(message){
                if(message.statusCode==200){
                    window.location.href="/admin_login.php";
                }else{
                    alert(message.message);
                }
            },"json");
        }
        return false;
    })


    function checkPass(){
        var old_pass = $("input[name='old_pass']").val();
        var new_pass = $("input[name='new_pass']").val();
        var new_pass_agin = $("input[name='new_pass_agin']").val();
        var pattern  = /^[\d]{0,6}$/;
        if(old_pass=='' || new_pass=='' || new_pass_agin==''){
            alert('你有值没有填,请填写');
            return false;
        }else if(new_pass!=new_pass_agin){
            alert('两次密码输入不一致!');
            return false;
        }else if(pattern.test(new_pass)){
            alert('新密码设置过于简单,请重新设置!');
            return false;
        }
        return true;
    }
</script>