<meta charset="utf-8" />
<?php
date_default_timezone_set("Asia/Shanghai");
include_once ('../config.php');
session_start();
$login_type = $_SESSION['login_type'];
if ($login_type == 1) {
	header("Location:../index.php");
}
$reguser = $_POST['username'];
$regpass1 = $_POST['password'];
$regpass2 = $_POST['repassword'];
$regnickname = $_POST['nickname'];
$regemail = $_POST['email'];
$ntime = date('Y-m-d', time());
if ($regpass1 == $regpass2) {
	if (!empty($reguser) && !empty($regpass1) && !empty($regpass2) && !empty($regnickname) && !empty($regemail)) {
		if (!preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $reguser) || !preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $regnickname)) {
			$userlong = mb_strlen($reguser, 'UTF-8');
			$passlong = mb_strlen($regpass1, 'UTF-8');
			$nicklong = mb_strlen($regnickname, 'UTF-8');
			if ($userlong > 6 && $userlong < 16 && $passlong > 6 && $passlong < 32 && $nicklong > 6 && $nicklong < 32) {
				$regpassmd5=md5($regpass1);
				$con = mysql_connect($mysql_address, $mysql_user, $mysql_password);
				if (!$con) {
					die("数据库连接失败" . mysql_error());
				} else {
					mysql_select_db($mysql_dbname, $con);
					mysql_query("SET NAMES utf8");
					$checkquery= 'SELECT * FROM user WHERE username="'.$reguser.'"';
					$checkresult=mysql_query($checkquery);
					$checkrow=mysql_fetch_array($checkresult);
					if (!empty($checkrow['UID'])){
						$_SESSION['reg_error']="注册失败:用户名已经存在";
						header("Location:register.php");
						mysql_close($con);
						exit;
					}
					$query = "INSERT INTO `chstube`.`user` (`UID`, `username`, `password`, `email`, `level`, `regtime`, `nickname`, `money`) VALUES (NULL, '" . $reguser . "', '" . $regpassmd5 . "', '" . $regemail . "', '1', '" . $ntime . "', '" . $regnickname . " ', '0')";
					mysql_query($query);
					mysql_close($con);
					$_SESSION['nolog']="注册成功 请登录";
					header("Location:../index.php");
				}
			}else{
				$_SESSION['reg_error']="注册失败:字段达不到要求 (长度/内容)";
				header("Location:register.php");
			}
		} else {
			$_SESSION['reg_error'] = "注册失败:用户名或者昵称中不能包含特殊字符";
			header("Location:register.php");
		}
	} else {
		$_SESSION['reg_error'] = "注册失败:请认真填写每一项内容";
		//DEBUG 使用此DEBUG请注释掉header
		/*echo "RegUser=".$reguser;
		echo "</br>";
		echo "RegPass1=".$regpass1;
		echo "</br>";
		echo "RegPass2=".$regpass2;
		echo "</br>";
		echo "RegNickNmae=".$regnickname;
		echo "</br>";
		echo "RegEMail=".$regemail;
		echo "</br>";
		echo "NowTime=".$ntime;*/
		header("Location:register.php");
	}
} else {
	$_SESSION['reg_error'] = "注册失败:密码和重复密码不一致";
	header("Location:register.php");
}
?>