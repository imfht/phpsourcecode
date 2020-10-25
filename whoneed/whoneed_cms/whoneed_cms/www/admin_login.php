<?php
	$strCommonTitle = 'Whoneed智能系统';
	$strCommonUrl	= 'http://www.whoneed.com';
?>
<html><head><title><?php echo $strCommonTitle;?> -- 管理后台</title>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<link href="/static/admin/css/style.css" type=text/css rel=stylesheet>
<script language=Javascript src="/static/admin/js/login.js"></script>
<script language=Javascript type=text/Javascript> 
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
 
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
 
function MM_nbGroup(event, grpName) { //v6.0
  var i,img,nbArr,args=MM_nbGroup.arguments;
  if (event == "init" && args.length > 2) {
    if ((img = MM_findObj(args[2])) != null && !img.MM_init) {
      img.MM_init = true; img.MM_up = args[3]; img.MM_dn = img.src;
      if ((nbArr = document[grpName]) == null) nbArr = document[grpName] = new Array();
      nbArr[nbArr.length] = img;
      for (i=4; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
        if (!img.MM_up) img.MM_up = img.src;
        img.src = img.MM_dn = args[i+1];
        nbArr[nbArr.length] = img;
    } }
  } else if (event == "over") {
    document.MM_nbOver = nbArr = new Array();
    for (i=1; i < args.length-1; i+=3) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = (img.MM_dn && args[i+2]) ? args[i+2] : ((args[i+1])? args[i+1] : img.MM_up);
      nbArr[nbArr.length] = img;
    }
  } else if (event == "out" ) {
    for (i=0; i < document.MM_nbOver.length; i++) {
      img = document.MM_nbOver[i]; img.src = (img.MM_dn) ? img.MM_dn : img.MM_up; }
  } else if (event == "down") {
    nbArr = document[grpName];
    if (nbArr)
      for (i=0; i < nbArr.length; i++) { img=nbArr[i]; img.src = img.MM_up; img.MM_dn = 0; }
    document[grpName] = nbArr = new Array();
    for (i=2; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = img.MM_dn = (args[i+1])? args[i+1] : img.MM_up;
      nbArr[nbArr.length] = img;
  } }
}
//-->
</script>
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
<script> 
function enter(field,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
 
	if (keycode == 13)
	{
	   checkLoginForm();
	}
}
 
</script>
 
<CENTER><FONT face="Verdana, Arial" color=red size=1><B></B></FONT> 
<form name=form1 onSubmit="return checkLoginForm();" method=post action='/admin/site/login'>
<table cellSpacing=0 cellPadding=0 width=344 align=center bgColor=#cccccc 
border=0>
  <tbody>
  <tr>
    <td valign="top"><img height=2 width=1><table cellSpacing=0 cellPadding=0 width=330 align=center bgColor=#f5f5f5 
      border=0>
        <tbody>
        <tr>
          <td><img src="/static/admin/images/login_header.gif"></td></tr>
        <tr>
          <td class=maindesc align=middle><br>Enter your username and password 
            in the form below. </a>. <br><br>
            <table cellSpacing=0 cellPadding=4 width=250 align=center 
            bgColor=#f5f5f5 border=0>
              <tbody>
              <tr>
                <td class=maindescbig>用户名:</td>
                <td align=left><input onkeypress="return enter(this, event);" 
                  value="admin" name=User size=18></td></tr>
              <tr>
                <td class=maindescbig>密　码:</td>
                <td align=left><input onkeypress="return enter(this, event);" 
                  type=password value="" name=Pass size=18></td></tr>
			  <tr>
                <td class=maindescbig>验证码:</td>
                <td align=left><input onkeypress="return enter(this, event);" 
                  type=text value="" name=authCode size=6> ←<img src="/static/plug-in/verifyCode/authimg.php" align='top'></td></tr>				  
              <tr>
                <td align=middle colSpan=2><a 
                  onmouseover="MM_nbGroup('over','login','/static/admin/images/button_login_over.jpg','/static/admin/images/button_login_down.jpg',1)" 
                  onclick="MM_nbGroup('down','group1','login','',1); checkLoginForm();" 
                  onmouseout="MM_nbGroup('out')" 
                  href="#"><img 
                  alt=Login src="/static/admin/images/button_login_normal.jpg" onload="" 
                  border=0 name=login></a>&nbsp; <a 
                  onmouseover="MM_nbGroup('over','cancel','/static/admin/images/button_cancel_over.jpg','/static/admin/images/button_cancel_down.jpg',1)" 
                  onclick="MM_nbGroup('down','group1','cancel','',1); document.form1.reset();" 
                  onmouseout="MM_nbGroup('out')" 
                  href="#"><img 
                  alt=Cancel src="/static/admin/images/button_cancel_normal.jpg" 
                  onload="" border=0 name=cancel></a>&nbsp; 
          </td></tr></tbody></table></td></tr></tbody></table><img width=1 height=2></a></td></tr></tbody></table></form>
<br><br>
<div class=maindesc align=center>Powered by <b><a href="<?php echo $strCommonUrl;?>" title="<?php echo $strCommonTitle;?>" target="_blank"><span style="color: #FF9900"><?php echo $strCommonTitle;?></span></a></b> Copyright &copy; <?php echo date('Y', time()); ?> All Rights Reserved .</div></td></tr></table><br></CENTER></body></html>
