<?php
/*
eedo留言系统
作者：avenjan
文件功能：登录系统
*/
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
if(!isset($_POST["u"])){ 
    exit('非法访问!'); 
}
$username = htmlspecialchars($_POST["u"]); 
$pwd = MD5($_POST["p"]); 
$datas = $database->select("admin", "*",[
    "uname[=]" => $username
]);  
if(empty($datas)){
echo "用户不存在";
$database->insert("log", array(
 "id" => date('YmdHis', time()),
 "data" => date("Y-m-d"),
 "info" => date('Y-m-d H:i:s',time())." / 尝试使用".$username."登录系统，<span style='color:red'>访问拒绝</span>"
)); 
}else{
	if($pwd==$datas[0]['password']){
		setcookie('username',$username, time()+3600*24,'/');
		setcookie('u_name',$datas[0]['name'], time()+3600*24,'/');
		$database->insert("log", array(
		 "id" => date('YmdHis', time()),
		 "data" => date("Y-m-d"),
		 "info" => date('Y-m-d H:i:s',time())." / ".$username."<span style='color:red'>成功登录系统</span>"
		)); 
		echo "OK";
	}else{
		echo "账号密码错误请重试!";
		$database->insert("log", array(
		 "id" => date('YmdHis', time()),
		 "data" => date("Y-m-d"),
		 "info" => date('Y-m-d H:i:s',time())." / ".$username."登录系统失败，失败信息：<span style='color:red'>密码错误</span>"
		)); 
	}
}
?>