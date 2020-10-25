<?php
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
empty($_POST["sendsms"])? $_POST["sendsms"] ="off":"";
empty($_POST["sendcontent"])? $_POST["sendcontent"] ="off":"";
empty($_POST["viewcity"])? $_POST["viewcity"] ="off":"";
	foreach($_POST as $k=>$v){
	$info = $database->update("system", ["val" => $v], ["name" => $k]);
	}
	$debug = $database->error();
if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
$database->insert("log", array(
	 "id" => date('YmdHis', time()),
	 "data" => date("Y-m-d"),
	 "info" => date('Y-m-d H:i:s',time()).$_COOKIE['u_name']."修改系统配置参数"
	));
}else{
	echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];

}
?>