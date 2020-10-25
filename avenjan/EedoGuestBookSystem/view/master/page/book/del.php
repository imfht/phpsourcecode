<?php
/*
eedo留言系统
作者：avenjan
文件功能：删除留言及关联回复

*/
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
//echo $_POST["sid"];
$database->delete("book",array("id[=]" => $_POST["sid"])); //删除留言
$database->delete("replay",array("bid[=]" => $_POST["sid"]));  //删除关联回复
if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
$database->insert("log", array(
 "id" => date('YmdHis', time()),
 "data" => date("Y-m-d"),
 "info" => date('Y-m-d H:i:s',time()).$_COOKIE['u_name']."删除留言,ID:". $_POST["sid"]
)); 
}else{
	echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];

}
?>