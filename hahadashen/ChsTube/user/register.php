<!--
国内端
-->
<?php
session_start();
include_once ('../config.php');
if (!empty($_SESSION['login_type'])) {
	if ($_SESSION['login_type'] == 1) {
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
								<a class="active" href="register.php">
									注册
								</a>
							</li>
							<li>
								<a  href="login.php">
									登录
								</a>
							</li>
						</ul>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
			<div class="row well">
				<div class="col-lg-6" align="center">
					<h1>Chstube-注册</h1>
					<h3>很高兴您能加入我们</h3>
					<label class="label label-danger <?php
						if (empty($_SESSION['reg_error'])) {
							echo "hide";
						}
					?>">
					<?php
					echo $_SESSION['reg_error'];
					session_unset($_SESSION['reg_error']);
					?></label>
					<form action="regdo.php" method="post">
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i>&nbsp;用户名</span>
							<input type="text" id="username" name="username" placeholder="请输入用户名" class="form-control"/>
						</div>
					</br>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-tags"></i>&nbsp;昵称</span>
							<input type="text" id="nickname" name="nickname" placeholder="请输入昵称" class="form-control" />
						</div>
					</br>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i>&nbsp;密码</span>
							<input type="password" id="password" name="password" placeholder="请输入密码" class="form-control"/>
						</div>
					</br>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i>&nbsp;重复密码</span>
							<input type="password" id="repassword" name="repassword" placeholder="请再次输入密码" class="form-control"/>
						</div>
					</br>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i>&nbsp;电子邮箱</span>
							<input type="email" id="email" name="email" placeholder="请输入电子邮箱" class="form-control" />
						</div>
					</br>
						<button type="submit" id="submit" class="btn btn-primary btn-lg">
						注册
						</button>
					</form>
				</div>
				<div class="col-lg-6">
				</br></br></br>
					<div class="alert alert-warning" role="alert">
						<strong><center>注册必读</center></strong></br>
						<strong>帐号:</strong>禁止分享您在本站的帐号 我们已经开启了IP智能识别 一旦检测到您的帐号分享给别人 我们将封禁您的帐号</br></br>
						<strong>版权:</strong>您从本站下载的所有视频的版权均归原作者所有 一旦产生版权纠纷 与本站无关</br></br>
						<strong>捐赠:</strong>所有捐赠不是必须的 我们不会给未捐赠者增加任何恶意的广告 无论您是否捐赠 我们都欢迎您</br></br>
						<strong>管理:</strong>唯一管理QQ:2440174045 咨询请直接说主题 无意义话题一律不回答</br></br>
						<strong>须知:</strong>一旦您注册 则默认代表同意以上全部协议 最终解释权归Chstube所有</br></br>
						<strong>最后:</strong>感谢您选择我们！
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
