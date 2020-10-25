<?php
session_start();
include_once ('../config.php');
if ($_SESSION['admin_login_type'] == 1) {
	$_SESSION['admin_sign'] = "您已经登录";
	header("Location:index.php");
} else {
}
?>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Chstube-管理员后台</title>
		<script type="text/javascript" src="../js/jquery.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.js" ></script>
		<link rel="stylesheet" href="../css/bootstrap.css" />
	</head>
	<body>
		<div class="container">
			<div class="row">
				</br></br></br>
				<div class="col-lg-12 well" align="center">
					<h1>请登录</h1>
					<p></p>
					<form action="logdo.php" method="post">
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i>用户名</span>
							<input type="text" id="username" name="username" placeholder="请输入用户名" class="form-control"/>
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i>密码</span>
							<input type="password" id="password" name="password" placeholder="请输入密码" class="form-control"/>
						</div>
						</br>
						</br>
						<label class="label label-info <?php if (empty($_SESSION['admin_sign'])){echo "hide";} ?>"><?php echo $_SESSION['admin_sign'];
						unset ($_SESSION['admin_sign']);
						?></label>
						</br>
						<button type="submit" id="submit" class="btn btn-primary btn-lg">
						登录
						</button>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>