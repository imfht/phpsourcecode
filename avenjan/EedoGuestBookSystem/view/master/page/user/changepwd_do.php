<?php
/*
eedo留言系统
作者：avenjan
文件功能：修改密码
*/
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
if(!isset($_POST["uname"])){ 
    exit('非法访问!'); 
}
$uname = htmlspecialchars($_POST["uname"]); 
$pwd = MD5($_POST["pwd"]); 
$database->update("admin", array(  
	"password" => $pwd
), array(  
    "uname[=]" => $uname  
)); 
if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
	$database->insert("log", array(
	 "id" => date('YmdHis', time()),
	 "data" => date("Y-m-d"),
	 "info" => date('Y-m-d H:i:s',time()).$_COOKIE['u_name']."修改用户密码"
	)); 
}else{
	echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];
} 
?>