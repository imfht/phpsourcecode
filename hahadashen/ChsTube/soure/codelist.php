<?php
session_start();
include_once ('../config.php');
if ($_SESSION['login_type']==1){
}else{
	$_SESSION['nolog']="请先登录";
	header("Location:../index.php");
}
$videoid=$_GET['vid'];
?>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Chstube-视频格式列表</title>
		<script type="text/javascript" src="../js/jquery.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.js" ></script>
		<link rel="stylesheet" href="../css/bootstrap.css" />
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="well col-lg-12" align="center">
					<div class="panel panel-success">
						<div class="panel-heading">
							此视频拥有的格式
						</div>
						<div class="panel-body"></div>
						<table class="table">
							<tr>
								<th>视频格式</th>
								<th>分辨率</th>
								<th>Youtube视频适配信息</th>
								<th>下载通道</th>
							</tr>
							<?php
							$con = mysql_connect($mysql_address, $mysql_user, $mysql_password);
							if (!$con) {
								$_SESSION['nolog'] = "数据库链接失败";
								header("Location:../index.php");
							} else {
								mysql_select_db($mysql_dbname, $con);
								mysql_query("SET NAMES utf8");
								$query = "SELECT * FROM video_info WHERE vid=" . $videoid;
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result)){
									if ($row['link']=="Waiting"){
										$link='<a href="#" class="btn btn-primary disabled">转码中 请等待</a>';
									}else{
										$link='<a href="'.$link.'" class="btn btn-primary">国内极速通道</a>';
									}
									echo "<tr>";
									echo '<td><label class="label label-info">'.$row['ext'].'</label>';
									echo '<td><label class="label label-primary">'.$row['res'].'</label>';
									echo '<td><label class="label label-default">'.$row['note'].'</label>';
									echo '<td>'.$link.'</td>';
									echo '</tr>';
								}
							}
							?>
						</table>
					</div>
				</div>
			</div>
	</body>
</html>
