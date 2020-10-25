<!--
国内端
-->
<?php
include_once ('config.php');
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>在线Youtube转码下载工具</title>
		<script type="text/javascript" src="js/jquery.js" ></script>
		<script type="text/javascript" src="js/bootstrap.js" ></script>
		<link rel="stylesheet" href="css/bootstrap.css" />
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
							<li class="active">
								<a href="index.php">
									Youtube视频下载
								</a>
							</li>
						</ul>
						<?php
if($_SESSION['login_type']==1){
?>
<ul class="nav navbar-nav navbar-right">
<li>
<a href="user/user.php">
你好 <?php echo $_SESSION['nickname'] ?>
</a>
</li>
<li>
<a href="user/logout.php">
退出登录
</a>
</li>
</ul>
<?php
}else{
?>
<ul class="nav navbar-nav navbar-right">
<li>
<a href="user/register.php">
注册
</a>
</li>
<li>
<a href="user/login.php">
登录
</a>
</li>
</ul>
<?php } ?>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
			<div class="alert alert-info col-lg-12 <?php
			if (empty($_SESSION['nolog'])) { echo "hide";
			}
			?>" role="alert">
				<strong>提示:</strong><?php
				echo $_SESSION['nolog'];
				unset($_SESSION['nolog']);
				?>
			</div>
			<div class="well col-lg-12">
				<div class="jumbotron" align="center">
					<h1>欢迎来到Chstube</h1>
					<p>
						高速国内Youtube视频服务
					</p>
					<p>
						<a class="btn btn-primary btn-lg" href="#" role="button">
							开始体验
						</a>
						&nbsp;
						<a class="btn btn-primary btn-lg" href="user/user.php" role="buttom">
							个人中心
						</a>
					</p>
				</div>
				<div class="container col-lg-12">
					<div class="jumbotron col-lg-6" align="center">
						<h3>输入Youtube视频ID进行解析</h3>
						<p>
							https://www.youtube.com/watch?v=XXXXXXXXXXX</br>XXXXXXXXX即为视频ID</br>
							<form action="soure/check.php" method="post">
								<input id="vid" name="vid" placeholder="输入视频ID" type="text" style="width: 100%; height: 40px;"/>
								</br>
								<button class="btn btn-primary btn-lg" type="submit" value="开始解析">
								开始解析
								</button>
							</form>
						</p>
						<p></p>
					</div>
					<div class="alert alert-info col-lg-6" role="alert">
						<strong>这里有一些你必须看的提示</strong>
						</br></br>
						<strong>视频下载的原理:</strong>如果您下载的视频曾经被别的用户解析过 那么您可以直接从我们的服务器获取此视频,如果没有 您的任务将会被提交到服务器 我们将对所有用户提交的视频依照顺序进行压制和转码(音视频)来达到您的要求</br></br> <strong>我想快速得到视频怎么办:</strong>我们的服务免费为每位用户提供 如果您需要优先服务 也可以购买VIP 我们有专用的 稳定快速的服务器为您提供转码压制服务 同时也支持您上传您自己的视频进行转码压制</br></br> <strong>提交了任务没有看到:</strong>因为我们的列表同步为异步同步方式 当您提交后就已经进入队列 因为延迟 任务将会在1-5分钟内显示出来
						</br></br>
						<strong>声明:</strong>本站所有视频的版权归原作者所有 本站仅辅助转码 版权视频请在下载后24小时删除 谢谢</br>&nbsp;
					</div>
				</div>
				<div class="container col-lg-12">
					<div class="progress">
						<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
							<i class="glyphicon glyphicon-refresh"></i>&nbsp;服务器娘在努力工作中 <span class="sr-only">100% Complete</span>
						</div>
					</div>
				</div>
				<div class="container col-lg-12">
					<div class="panel panel-success">
						<div class="panel-heading">
							正在进行的任务
						</div>
						<div class="panel-body">
							<p>
								这里显示当前Chstube服务器正在进行的任务</br>
								任务状态:
								<?php
								$con = mysql_connect($mysql_address, $mysql_user, $mysql_password);
								if (!$con) {
									die('数据库链接失败:' . mysql_error());
								} else {
									mysql_select_db($mysql_dbname, $con);
									mysql_query("SET NAMES utf8");
									$result = mysql_query("SELECT * FROM do");
									while ($row = mysql_fetch_array($result)) {
										if ($row['type'] == 0) {
											echo '<label class="label label-success">成功</label>';
										} elseif ($row['type'] == 1) {
											echo '<label class="label label-danger">错误</label>';
										}
										echo '上次刷新时间:<label class="label label-success">' . $row['time'] . '</label>';
									}
								}
								?>
							</p>
						</div>
						<table class="table">
							<tr>
								<th>名称</th>
								<th>选项</th>
								<th>链接</th>
								<th width="40%">进度</th>
								<th>操作</th>
							</tr>
							<?php
							$con = mysql_connect($mysql_address, $mysql_user, $mysql_password);
							if (!$con) {
								die('数据库连接失败: ' . mysql_error());
							} else {
								mysql_select_db($mysql_dbname, $con);
								mysql_query("SET NAMES utf8");
								$result = mysql_query("SELECT * FROM queue");
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
									echo "<td>" . $row['name'] . "</td>";
									echo "<td>" . $video_status . "</td>";
									echo '<td><a href=' . $row['URL'] . ' target="_blank" class="btn btn-primary">原视频地址</a></td>';
									echo '<td><div class="progress">
							<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: ' . $row['now'] . '%;">' . $row['now'] . '%<span class="sr-only">100% Complete</span>
							</div>
							</div></td>';
									if ($row['type'] == 3) {
										$control = '<a href="soure/codelist.php?vid='.$row['id'].'" class="btn btn-primary" target="_blank">查看视频支持格式</a>';
									}else{
										$control = '';
									}
									echo "<td>", $control, "</td>";
									echo "</tr>";
								}
							}
							mysql_close($con);
							?>
						</table>
					</div>
				</div>
			</div>
		</div>
		</div>
	</body>
</html>