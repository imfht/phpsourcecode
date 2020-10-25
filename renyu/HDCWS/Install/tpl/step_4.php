<?php if (!defined('INSTALL_STATUS')) exit('Access Denied!')?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>HDCWS安装向导</title>
<link rel="stylesheet" href="css/global.css" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
</head>
<body>
	<?php require 'tpl/header.php';?>
	<div class="main">
		<div class="step">
			<ul>
				<li><em>1</em>检测环境</li>
				<li><em>2</em>创建数据</li>
				<li class="current"><em>3</em>完成安装</li>
			</ul>
		</div>
		<div class="process"><div class="process_go">0%</div></div>
		<div class="install_process"></div>
		<div class="action">
		<a href="javascript:history.go(-1);" class="btn_blue">上一步</a><a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="btn_x pre" id="finish">安装中..</a>
	</div>
<script type="text/javascript">
var timerId;
var num =0;
$(function(){
	//install(0);
	postData();
	$('.install_process').append('<p>数据初始化中...</p>').scrollTop(500000);
	timerId = setInterval(setNext,1500);

})

function postData() {
	$.post('index.php?step=4',{'act':'db'},function(data){
		clearInterval(timerId);
		if(data.status == 'error') {
			
			$('.install_process').append('<p style="color:red;">'+data.info+'</p>').scrollTop(500000);				
			$('.install_process').append('<p style="color:red;">安装失败,请重新安装</p>');		
			$('#finish').removeClass('pre');
			$('#finish').html('安装失败');
			//alert(data.info);
			return false;
		}else if(data.status == 'success_all') {			
			$('.process_go').width('100%');
			$('.process_go').html('100%');
			$('.install_process').append('<p>'+data.info+'</p>').scrollTop(500000);
			//$('.install_process').append("<font color='green'>缓存更新成功</font><br>");
			$('.install_process').append('<p style="color:green;">安装完成</p>');
			$('#finish').removeClass('pre');
			$('#finish').html('安装完成');
			setTimeout(function(){window.location.href = 'index.php?step=5'},1000);
		}
	},'json');
}

function setNext() {
	num = num+10;
	$('.process_go').width(num+'%');
	$('.process_go').html(num+'%');
	if(num >= 100) {
		num = 0;
	}
}
</script>
</body>
</html>