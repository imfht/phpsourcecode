<?php
session_start();
include_once ('../config.php');
if($_SESSION['login_type'] == 1){
}else{
	$_SESSION['nolog']="要提交链接请先登录或者注册";
	header("Location:../index.php");
}
$videoid=$_POST['vid'];
if (empty($videoid)){
	$_SESSION['nolog']="视频ID为空";
	header("Location:../index.php");
}
$url = "http://url.chstube.com/title.php?id=".$videoid; 
$ch = curl_init(); 
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10); 
$title = curl_exec($ch);
if (empty($title)){
	$_SESSION['nolog']="视频ID无效";
	header("Location:../index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8" />
		<title>链接提交</title>
		<script type="text/javascript" src="../js/jquery.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.js" ></script>
		<link rel="stylesheet" href="../css/bootstrap.css" />
	</head>
	<body>
		<div class="container">
		</br>
		</br>
		</br>
		</br>
		<div class="well col-lg-12">
			<div class="col-lg-12" align="center">
				<h1>链接解析结果</h1>
			</br>
				<p>视频来源:<label class="label label-success">Youtube</label></p>
				<p>视频名称:<label class="label label-success"><?php echo $title; ?></label></p>
				<p>视频封面</p>
				<p><iframe src="http://url.chstube.com/pic.php?id=<?php echo $videoid;?>" width="500" height="380" scrolling="no" frameborder=0 border=0 ></iframe></p>
				<p></p>
				<p><a href="putup.php?id=<?php echo $videoid; ?>" class="btn btn-primary btn-lg">提交任务<i class="glyphicon glyphicon-upload"></i></a>&nbsp;<a href="../index.php" class="btn btn-default btn-lg">返回主页<i class="glyphicon glyphicon-home"></i></a></p>
			</div>
			<div class="alert alert-info col-lg-12" role="alert">
				<strong>使用向导:</strong>确认后任务将提交到解析服务器 在不拥堵的情况下解析需要2-4秒 请稍候前往个人中心选择转码选项
			</div>
		</div>
	</body>
</html>