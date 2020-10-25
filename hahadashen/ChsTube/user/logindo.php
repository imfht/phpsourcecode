<meta charset="utf-8" />
<?php
session_start();
$getuser=$_POST['username'];
$getpass=$_POST['password'];
$login_type=$_SESSION['login_type'];
if (!empty($login_type)){
	if ($login_type==1){
		header("Location:../index.php");
	}
}
include_once ('../config.php');
$con=mysql_connect($mysql_address,$mysql_user,$mysql_password);
if (!$con){
	die ('数据库连接失败'.mysql_error());
}else{
	mysql_select_db($mysql_dbname,$con);
	mysql_query("SET NAMES utf8");
	$result=mysql_query('SELECT * FROM user WHERE username="'.$getuser.'"');
$row = mysql_fetch_array($result);
/*
echo "当前系统运行在DEBUG模式下</br>";
echo "=====DEBUG INFO=====</br>";
echo "</br>";
echo "GetUser=".$getuser;
echo "</br>";
echo "GetPass=".$getpass;
echo "</br>";
echo "GetPassMD5=".md5($getpass);
echo "</br>";
echo "</br>";
echo "=====MYSQL INFO=====</br>";
echo "</br>";
echo "UID=".$row['UID'];
echo "</br>";
echo "User=".$row['username'];
echo "</br>";
echo "Pass[MD5]=".$row['password'];
echo "</br>";
echo "Level=".$row['level'];
echo "</br>";
echo "RegTime=".$row['regtime'];
echo "</br>";
echo "NickNmae=".$row['nickname'];
echo "</br>";
echo "Money=".$row['money'];
echo "</br>";
*/
$md5getpass=md5($getpass);
if ($md5getpass==$row['password']){
	$_SESSION['login_type']=1;
	$_SESSION['nickname']=$row['nickname'];
	$_SESSION['username']=$row['username'];
	$_SESSION['level']=$row['level'];
	$_SESSION['money']=$row['money'];
	$_SESSION['regtime']=$row['regtime'];
	$_SESSION['UID']=$row['UID'];
	$_SESSION['email']=$row['email'];
	mysql_close($con);
	$_SESSION['nolog']="登录成功";
	header("Location:../index.php");
}else{
	mysql_close($con);
	$_SESSION['nolog']="登录失败";
	header("Location:../index.php");
}
mysql_close($con);
}
?>