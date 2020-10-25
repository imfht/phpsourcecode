<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
  
require_once (dirname(__FILE__) . "/inc/config.inc.php");
$gd = gd_info();
$curl = function_exists('curl_init');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Winner权限管理系统 - 安装向导</title>
<script language="javascript">
<!-- 
function onNext(){
	var data = document.getElementsByName("data");
	var num = 0;
	for(i=0;i<data.length;i++){
		if(data[i].value==1){
			num += Number(data[i].value);
		}
	}
	if(num==data.length){
		window.location = "mysql.php";
	}
	num = 0;
}

function onLast(){
	history.back();
}
-->
</script>
<link href="img/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div align="center">
<div class="main">
<div class="top">
  <img src="img/logo_about.png" height="45" />
  <span>Winner权限管理系统</span></div>
 <div class="content">
  <table width="100%" border="0" cellspacing="0">
  <tr>
    <td height="26" colspan="4" class="top_txt">检查环境</td>
    </tr>
  <tr class="top2_txt">
    <td width="25%" height="24">项目</td>
    <td width="25%">&nbsp;最低配置</td>
    <td width="25%">&nbsp;标准配置</td>
    <td width="25%">&nbsp;当前服务器</td>
  </tr>
  <tr>
    <td width="25%" height="22">操作系统</td>
    <td width="25%">&nbsp;类Unix/WINTN</td>
    <td width="25%">&nbsp;类Unix</td>
    <td width="25%">&nbsp;<?php echo PHP_OS; ?></td>
  </tr>
  <tr>
    <td width="25%" height="22">PHP版本</td>
    <td width="25%">&nbsp;5.3</td>
    <td width="25%">&nbsp;5.3</td>
    <td width="25%">&nbsp;<?php echo PHP_VERSION; ?></td>
  </tr>
  <tr>
    <td width="25%" height="22">附件上传</td>
    <td width="25%">&nbsp;2M</td>
    <td width="25%">&nbsp;2M</td>
    <td width="25%">&nbsp;<?php echo ini_get("upload_max_filesize"); ?></td>
  </tr>
  <tr>
    <td width="25%" height="22">GD 库</td>
    <td width="25%">&nbsp;支持</td>
    <td width="25%">&nbsp;支持</td>
    <td width="25%">&nbsp;<?php echo $gd['GD Version']?'支持':'不支持'; ?></td>
  </tr>
  <tr>
    <td width="25%" height="22">CURL 库</td>
    <td width="25%">&nbsp;支持</td>
    <td width="25%">&nbsp;支持</td>
    <td width="25%">&nbsp;<?php echo $curl?'支持':'不支持'; ?></td>
  </tr>
  
</table>
<table width="100%" border="0" cellspacing="0">
  <tr>
    <td height="26" colspan="3" class="top_txt">写入权限</td>
    </tr>
  <tr class="top2_txt">
    <td width="33%" height="24">项目&nbsp;</td>
    <td width="34%">&nbsp;所需状态</td>
    <td width="33%">&nbsp;当前状态</td>
  </tr>
  <tr>
    <td height="22">../Conf</td>
    <td width="34%">&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/yes.gif" width="22" height="22" /></td>
    <td width="33%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo is_writable(CONF)==1?'<img src="img/yes.gif" width="22" height="22" />':'<img src="img/no.gif" width="22" height="22" />'; ?><input name="data" type="hidden" value="<?php echo is_writable(CONF) ?>" /></td>
  </tr>
  <tr>
    <td height="22">../Conf/Backup</td>
    <td width="34%">&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/yes.gif" width="22" height="22" /></td>
    <td width="33%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo is_writable(CONF.'/Backup')==1?'<img src="img/yes.gif" width="22" height="22" />':'<img src="img/no.gif" width="22" height="22" />'; ?><input name="data" type="hidden" value="<?php echo is_writable(CONF.'/Backup') ?>" /></td>
  </tr>
  <tr>
    <td height="22">../Uploads</td>
    <td width="34%">&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/yes.gif" width="22" height="22" /></td>
    <td width="33%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo is_writable(UPLOAD)==1?'<img src="img/yes.gif" width="22" height="22" />':'<img src="img/no.gif" width="22" height="22" />'; ?><input name="data" type="hidden" value="<?php echo is_writable(UPLOAD) ?>" /></td>
  </tr>
  <tr>
    <td height="22">../Runtime</td>
    <td width="34%">&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/yes.gif" width="22" height="22" /></td>
    <td width="33%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo is_writable(RUNTIME)==1?'<img src="img/yes.gif" width="22" height="22" />':'<img src="img/no.gif" width="22" height="22" />'; ?><input name="data" type="hidden" value="<?php echo is_writable(RUNTIME) ?>" /></td>
  </tr>
  <tr>
    <td height="22">../Conf/Session</td>
    <td width="34%">&nbsp;&nbsp;&nbsp;&nbsp;<img src="img/yes.gif" width="22" height="22" /></td>
    <td width="33%">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo is_writable(CONF.'/Session')==1?'<img src="img/yes.gif" width="22" height="22" />':'<img src="img/no.gif" width="22" height="22" />'; ?><input name="data" type="hidden" value="<?php echo is_writable(CONF.'/Session') ?>" /></td>
  </tr>
</table>
 </div> 
 <div class="act"><input onclick="onLast()" class="but" name="yes" type="button" value="上一步" /> &nbsp; <input onclick="onNext()" class="but" name="no" type="button" value="下一步" />
 <div><img src="img/step2.png" width="700" height="10" /></div>
 </div>
 <div class="foot">Copyright 2010-2015 <a href="http://www.95era.com/" target="_blank">九五时代</a> Inc.   All Rights Reserved</div>
</div>
</div>
</body>
</html>
