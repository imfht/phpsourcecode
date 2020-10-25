<?php
	session_start();

	if(!isset($_SESSION['account'])) {
		echo "<script>window.location.href='./login.php';</script>";
	}
	
	else {
		echo "<script>window.location.href='./admin.php';</script>";
	}
?>