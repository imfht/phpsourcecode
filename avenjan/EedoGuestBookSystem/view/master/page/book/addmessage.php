<?php
/*
eedo留言系统
作者：avenjan
文件功能：新增回复

*/
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
if(empty($_POST['rename'])){
	$rename=$_COOKIE['u_name'];
}else{
	$rename=$_POST['rename'];
}
$id=date('YmdHis', time());
$database->insert("replay", array( 
	"id" => $id,
	"bid" => $_GET["sid"] ,
	"name" => $rename,
	"content" => $_POST["recontent"],
	"time" => time(),
	"ip" => getIp()
));  
if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
$database->insert("log", array(
 "id" => date('YmdHis', time()),
 "data" => date("Y-m-d"),
 "info" => date('Y-m-d H:i:s',time()).$_COOKIE['u_name']."回复留言,ID:". $_GET["sid"]
));
}else{
	echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];

}
?>