<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
session_start();
if(file_exists('lock.txt')){
    echo '系统已安装，请不要重复安装！如需安装，请删除install文件夹下的lock.txt文件。';
    exit();
}
$mail = $_GET['mail'];
$total = intval($_GET['total']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Winner权限管理系统 - 安装向导</title>
<script type="text/javascript" src="img/jquery.js"></script>
<script type="text/javascript" src="img/install.js"></script>
<script language="javascript">
$(function(){
	$("#show").append("成功创建数据库！\r\n");
});
var info = '<?php echo $total; ?>';
if(info){
	toInstall('<?php echo $mail; ?>');
}


var num = 0;
loopTable(num);

function loop(num){
	loopTable(num);
}


function loopTable(num){
	$(function(){
		var total = Number('<?php echo $total; ?>');
		if(num<=total){
			$.post("inc/putdata.act.php", {act:'redb',go:num},function(data){
				$("#show").append(data);
				$("#show").scrollTop(1000*total);
				num++;
				loop(num);
			});
		}else{
			$("#propress").html("数据库导入完成");
		}
	});
}

function onNext(){
	window.location = "../index.php";
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
    <td height="26" class="top_txt">数据库安装信息</td>
    </tr>
  <tr>
    <td height="22" id="propress" style="color:red">数据正在导入中, 请稍等片刻</td>
    </tr>
  <tr>
    <td height="22" align="center"><textarea style="width:99%; height:290px; font-size:12px;" id="show" cols=""></textarea></td>
    </tr>
</table>
 </div> 
 <div class="act"><input onclick="onNext()" class="but" name="no" type="button" value="立即体验" />
   
 <div><img src="img/step4.png" width="700" height="10" /></div>
 </div>
 <div class="foot">Copyright 2010-2015 <a href="http://www.95era.com/" target="_blank">九五时代</a> Inc.   All Rights Reserved</div>
</div>
</div>
</body>
</html>