<?php
	session_start(); 
	header("Content-Type: text/html; charset=UTF-8");
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	include_once(dirname(dirname(__FILE__)).'/config/config.php');
	
	$old = $_POST['oldpw'].'xiaoz';
	$old = md5($old);
	
	
	$db->power();		//判断权限
	$con = $db->connect();
	
	if(isset($_POST['sub'])) {
		if($old != $_SESSION['pass']) {
			echo "<script>alert('原密码错误！');</script>";
			echo "<script>window.history.go(-1);</script>";
			exit();
			return 0;
		}
		if($_POST['newpw1'] == $_POST['newpw2']) {
			$new = $_POST['newpw2'].'xiaoz';
			$new = md5($new);				//新密码使用MD5加密
			$account = $_SESSION['account'];
			$sql = "UPDATE `user` SET `pass`='$new' WHERE `account` = '$account'";
			$query = $db->query($sql,$con);
			echo "<script>alert('修改成功！');</script>";
			unset($_SESSION['account']);
			unset($_SESSION['nickname']);
			unset($_SESSION['pass']);
			echo "<script>window.location.href='./login.php';</script>";
			exit();
			return false;
		}
	}
	
	$smarty->display('changepw.html');
?>