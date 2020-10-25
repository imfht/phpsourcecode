<?php 
	session_start();
	header("Content-Type: text/html; charset=UTF-8");
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	include_once(dirname(dirname(__FILE__)).'/config/config.php');
	
	$username = $_POST['username'];
	$password = $_POST['password'].xiaoz;
	$password = md5($password);
	$yzm = $_POST['Captcha'];//获取验证码
	$sql = "SELECT * FROM `user` WHERE account = '$username'";
	$con = $db->connect();	
	
	if($_POST['sub']) {
		$query = $db->query($sql,$con);
		$result = $db->fetch_arr($query);
		if($yzm != $_SESSION["helloweba_char"]) {
			echo "<script>alert('验证码错误！');</script>";
			echo "<script>window.history.go(-1);</script>";
			exit();
			return false;
		}
		if($password != $result['pass']) {
			echo "<script>alert('用户名或密码错误！');</script>";
			echo "<script>window.history.go(-1);</script>";
			exit();
			return false;
		}
		if($password == $result['pass']) {
			$_SESSION['account'] = $result['account'];
			$_SESSION['nickname'] = $result['nickname'];
			$_SESSION['pass'] = $result['pass'];
		}
	}
	
	//此方法判断用户权限
	$db->power();

	if(isset($_SESSION['account'])) {
		$smarty->assign('nickname',$_SESSION['nickname']);
		$smarty->display('admin.html');
	}
	
	if($_GET['id'] == 'out') {
		unset($_SESSION['account']);
		unset($_SESSION['nickname']);
		unset($_SESSION['pass']);
		echo "<script>window.location.href='./login.php';</script>";
	}
?>