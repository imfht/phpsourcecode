<?php
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
 $database->update("type", array(  
   "name" => $_POST["name"]
), array(  
    "id[=]" => $_GET["sid"]  
)); 
if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
	$database->insert("log", array(
	 "id" => date('YmdHis', time()),
	 "data" => date("Y-m-d"),
	 "info" => date('Y-m-d H:i:s',time()).$_COOKIE['u_name']."修改分类,名称:".$_POST["name"]
	)); 
}else{
	echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];

}
?>