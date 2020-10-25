<?php
session_start();
include_once ('../config.php');
if ($_SESSION['admin_login_type']==1){
}else{
	header("Location:login.php");
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
			<div class="col-lg-12 well">
				<div class="col-lg-12 well" align="center">
					<h1>管理工具栏</h1>
				</div>
			</div>
		</div>
	</body>
</html>