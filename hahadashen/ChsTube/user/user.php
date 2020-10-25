<!--
国内端
-->
<?php
session_start();
include_once ('../config.php');
if (!empty($_SESSION['login_type'])) {
	if ($_SESSION['login_type'] == 1) {

	} else {
		header("Location:../index.php");
	}
}
//新浪IP库函数
function getIPLoc_sina($queryIP) {
	$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=' . $queryIP;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$location = curl_exec($ch);
	$location = json_decode($location);
	curl_close($ch);

	$loc = "";
	if ($location === FALSE)
		return "";
	if (empty($location -> desc)) {
		$loc = $location -> province . $location -> city . $location -> district . $location -> isp;
	} else {
		$loc = $location -> desc;
	}
	return $loc;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>在线Youtube转码下载工具</title>
		<script type="text/javascript" src="../js/jquery.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.js" ></script>
		<link rel="stylesheet" href="../css/bootstrap.css" />
	</head>
	<body>
		<div class="container">
			<nav class="navbar navbar-default" role="navigation">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">折叠菜单</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="#">
							Chstube
						</a>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li>
								<a href="http://play.chstube.com" target="_blank">
									Youtube在线播放
								</a>
							</li>
							<li>
								<a href="../index.php">
									Youtube视频下载
								</a>
							</li>
						</ul>
						<ul class="nav navbar-nav navbar-right">
								<li>
									<a href="user.php">
									你好 <?php echo $_SESSION['nickname'] ?>
									</a>
								</li>
								<li>
									<a href="logout.php">
										退出登录
									</a>
								</li>
							</ul>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
			<div class="row well">
				<div class="col-lg-6" align="center">
					<h1>个人信息</h1>
					<h3>嘘~不要让别人看到哦</h3>
				</br>
				<table class="table">
					<tr>
						<th>用户名</th>
						<th align="right"><label class="label label-primary"><?php echo $_SESSION['username']?></label></th>
					</tr>
					<tr>
						<th>密码</th>
						<th align="right"><label class="label label-primary">***********</label></th>
					</tr>
					<tr>
						<th>E-Mail</th>
						<th align="right"><label class="label label-primary"><?php echo $_SESSION['email']?></label></th>
					</tr>
					<tr>
						<th>昵称</th>
						<th align="right"><label class="label label-primary"><?php echo $_SESSION['nickname']?></label></th>
					</tr>
					<tr>
						<th>注册时间</th>
						<th align="right"><label class="label label-primary"><?php echo $_SESSION['regtime']?></label></th>
					</tr>
					<tr>
						<th>等级</th>
						<th align="right"><label class="label label-primary"><?php echo $_SESSION['level']?></label></th>
					</tr>
					<tr>
						<th>账户余额</th>
						<th align="right"><label class="label label-primary"><?php echo $_SESSION['money']?></label></th>
					</tr>
				</table>
				</div>
				<?php
				$nowip = getenv("HTTP_X_FORWARDED_FOR");
				if (empty($nowip)) {
					$nowip = getenv("REMOTE_ADDR");
				}
				?>
				<div class="col-lg-6">
					<div class="panel panel-success">
  <!-- Default panel contents -->
  <div class="panel-heading">帐号安全-登录IP记录</div>
  <div class="panel-body">
    <p>我们的网站已经启用登录IP记录</p>
  </div>
  <table class="table">
    <tr>
    	<th>时间</th>
    	<th>IP</th>
    	<th>地点</th>
    </tr>
    <tr>
    	<td>当前</td>
    	<td><?php echo $nowip; ?></td>
    	<td><?php echo getIPLoc_sina($nowip); ?></td>
    </tr>
  </table>
</div>
				</div>
			</div>
			<div class="row well">
				<div class="col-lg-12">
					<div class="panel panel-primary">
						<div class="panel-heading">您的任务列表</div>
						<div class="panel-body">
							<p>您所有任务列表</p>
						</div>
						<table class="table">
						<tr>
							<th>视频名称</th>
							<th>视频地址</th>
							<th>任务状态</th>
							<th width="30%">当前进度</th>
							<th>执行操作</th>
						</tr>
						<?php
						$con = mysql_connect($mysql_address, $mysql_user, $mysql_password);
						if (!$con) {
							echo "数据库链接错误";
							exit ;
						} else {
							mysql_select_db($mysql_dbname, $con);
							$query = "SELECT * FROM queue WHERE uid=" . $_SESSION['UID'];
							mysql_query("SET NAMES utf8");
							$result = mysql_query($query);
							while ($row = mysql_fetch_array($result)) {
								if ($row['type'] == 0) {
									$video_status = '<label class="label label-default">等待解析</label>';
								} elseif ($row['type'] == 1) {
									$video_status = '<label class="label label-primary">解析中</label>';
								} elseif ($row['type'] == 2) {
									$video_status = '<label class="label label-danger">解析失败</label>';
								} elseif ($row['type'] == 3) {
									$video_status = '<label class="label label-info">下载转码中</label>';
								} elseif ($row['type'] == 4) {
									$video_status = '<label class="label label-danger">任务失败</label>';
								} elseif ($row['type'] == 5) {
									$video_status = '<label class="label label-success">任务完成</label>';
								} else {
									$video_status = '<label class="label label-warning">数据异常</label>';
								}
								echo "<tr>";
								echo "<td>", $row['name'], "</td>";
								echo '<td><a class="btn btn-primary" href="', $row['URL'], '" target="_blank">点击打开</a></td>';
								echo '<td>', $video_status, '</td>';
								echo '<td><div class="progress">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: ' . $row['now'] . '%;">' . $row['now'] . '%<span class="sr-only">100% Complete</span>
							</div>
							</div></td>';
								if ($row['type'] == 5) {
									$button = '<a class="btn btn-primary" href="../soure/down.php?id=' . $row['id'] . '" target="_blank">点击下载</a>';
								} else {
									$button = '<a class="btn btn-primary disabled">任务进行中</a>';
								}
								echo '<td>', $button, '</td>';
								echo '</tr>';
							}
						}
						?>
					</table>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
