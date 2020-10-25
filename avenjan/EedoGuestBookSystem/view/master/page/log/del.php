<?php

header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";				
$database->delete("log",array("id[=]" => $_POST["sid"]));
if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
}else{
	echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];

}
?>