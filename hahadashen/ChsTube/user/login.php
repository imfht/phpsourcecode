<!--
国内端
-->
<?php
session_start();
include_once ('../config.php');
if (!empty($_SESSION['login_type'])){
	if($_SESSION['login_type'] == 1){
		header("Location:../index.php");
	}
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
								<a href="register.php">
									注册
								</a>
							</li>
							<li class="active">
								<a href="login.php">
									登录
								</a>
							</li>
						</ul>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
			<div class="row">
				<div class="well col-lg-12" align="center">
					<h1>Chstube-登录</h1>
					<form action="logindo.php" method="post">
						<div class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i>用户名</span>
						<input type="text" id="username" name="username" placeholder="请输入用户名" class="form-control"/>
						<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i>密码</span>
						<input type="password" id="password" name="password" placeholder="请输入密码" class="form-control"/>
						</div>
					</br>
				</br>
				<button type="submit" id="submit" class="btn btn-primary btn-lg">登录</button>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
