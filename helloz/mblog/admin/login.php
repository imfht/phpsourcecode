<?php
	session_start(); 
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	$code = $_POST['yzm'];
	
	
	if($_POST['sub']) {
		if($code != $_SESSION["helloweba_char"]) {
			echo "<script>alert('验证码错误！');</script>";
			echo "<script>window.history.go(-1);</script>";
			exit();
			return false;
		}
		echo "<script>alert('正确！');</script>";
	}
	
	$smarty->display('login.html');
?>