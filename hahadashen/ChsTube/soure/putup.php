<?php
session_start();
include_once ('../config.php');
if ($_SESSION['login_type'] == 1) {
} else {
	$_SESSION['nolog'] = "请先登录";
	header("Location:../index.php");
	exit ;
}
$putid = $_GET['id'];
if (empty($putid)) {
	$_SESSION['nolog'] = "提交异常 无法抓取到视频ID";
	header("Location:../index.php");
	exit ;
}
$url = "http://url.chstube.com/title.php?id=" . $putid;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
$title = curl_exec($ch);
$con = mysql_connect($mysql_address, $mysql_user, $mysql_password);
if (!$con) {
	die("数据库链接错误" . mysql_error());
} else {
	mysql_select_db($mysql_dbname, $con);
	mysql_query("SET NAMES utf8");
	if (empty($_GET['re'])) {
		$checkquery = 'SELECT * FROM queue WHERE name="' . $title . '"';
		$checkresult = mysql_query($checkquery);
		$checkrow = mysql_fetch_array($checkresult);
		if (!empty($checkrow['id'])) {
			mysql_close($con);
			$_SESSION['down_id'] = $putid;
			$_SESSION['down_title'] = $title;
			header("Location:download.php");
			exit ;
		}
	}
	mysql_query("SET NAMES utf8");
	$query = "INSERT INTO `chstube`.`queue` (`id`, `name`, `choose`, `URL`, `now`, `type`, `UID`) VALUES (NULL, '" . $title . "', '等待服务器解析视频详细信息', 'https://www.youtube.com/watch?v=" . $putid . "', '0', '0', '" . $_SESSION['UID'] . "');";
	mysql_query($query);
	mysql_close($con);
}
?>
<html>
	<head>
		<meta charset="utf-8" />
		<title>链接提交-Chstube</title>
		<script type="text/javascript" src="../js/jquery.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.js" ></script>
		<link rel="stylesheet" href="../css/bootstrap.css" />
	</head>
	<body>
		<div class="container">
			<div class="well col-lg-12">
				<div align="center">
					<h1>链接提交结果</h1>
					<p></p>
					<p>
						提交结果:<label class="label label-info">已经提交</label>
					</p>
					<p></p>
					<p><a href="../user/user.php">个人中心</a>&nbsp;<a href="../index.php">回到主页</a></p>
				</div>
			</div>
		</div>
	</body>
</html>