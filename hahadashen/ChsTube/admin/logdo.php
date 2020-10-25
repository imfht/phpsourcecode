<?php
session_start();
include_once ('../config.php');
if ($_SESSION['admin_login_type']==1){
	$_SESSION['admin_sign']="您已经登录";
	header("Location:index.php");
}else{
}
$getuser=$_POST['username'];
$getpass=$_POST['password'];
if (empty($getuser) || empty($getpass)){
	$_SESSION['admin_sign']="用户名或密码不能为空";
	header("Location:index.php");
	exit;
}else{
$con=mysql_connect($mysql_address,$mysql_user,$mysql_password);
if (!$con){
	echo "Mysql连接错误";
	exit;
}else{
	mysql_select_db($mysql_dbname,$con);
	$query='SELECT * FROM admin_user WHERE username="'.$getuser.'"';
	mysql_query("SET NAMES utf8");
	$result=mysql_query($query);
	$row=mysql_fetch_array($result);
	$getpassmd5=md5($getpass);
	if ($getpassmd5==$row['password']){
		$_SESSION['admin_login_type']=1;
		header("Location:index.php");
		exit;
	}else{
		$_SESSION['admin_sign']="用户名或者密码错误";
		header("Location:index.php");
		exit;
	}
}
}
?>
