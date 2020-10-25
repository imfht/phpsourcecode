<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
require_once (dirname(__FILE__) . "/inc/config.inc.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Winner权限管理系统 - 安装向导</title>
<script language="javascript">
<!-- 
function onLast(){
	history.go(-2);
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
    <td height="26" class="top_txt">错误提示信息</td>
    </tr>
  <tr>
    <td height="22" align="center"><textarea style="width:99%" name="show" cols="" rows="19"><?php echo $_GET['show'] ?></textarea></td>
    </tr>
</table>
 </div> 
 <div class="act"><input onclick="onLast()" class="but" name="yes" type="button" value="上一步" />
   <div><img src="img/step3.png" width="700" height="10" /></div>
 </div>
 <div class="foot">Copyright 2010-2015 <a href="http://www.95era.com/" target="_blank">九五时代</a> Inc.   All Rights Reserved</div>
</div>
</div>
</body>
</html>
