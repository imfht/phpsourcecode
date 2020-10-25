<?php
/*
eedo留言系统
作者：avenjan
文件功能：编辑留言

*/
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
$database->update("book", array(  
	"type" => $_POST["type"],
	"title" => $_POST["title"],
	"content" => $_POST["content"],
	"name" => $_POST["name"],
	"phone" => $_POST["phone"],
	"email" => $_POST["email"],
	"view" => $_POST["view"]
), array(  
    "id[=]" => $_GET["sid"]  
));  
if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
$database->insert("log", array(
 "id" => date('YmdHis', time()),
 "data" => date("Y-m-d"),
 "info" => date('Y-m-d H:i:s',time()).$_COOKIE['u_name']."修改留言,ID:". $_GET["sid"]
));
}else{
	echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];
}
?>