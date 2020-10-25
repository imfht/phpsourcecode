<?php
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
$hasmsg = count_tab("book","type","=",$_POST["sid"]); //统计属于将删除分类下是留言条数
if($hasmsg<1){
	$sitename= $database->select("type","name",["id[=]" => $_POST["sid"]]);
	$database->insert("log", array(
	 "id" => date('YmdHis', time()),
	 "data" => date("Y-m-d"),
	 "info" => date('Y-m-d H:i:s',time()).$_COOKIE['u_name']."删除分类:".$sitename[0]
	)); 
	$database->delete("type",array("id[=]" => $_POST["sid"]));
	if(empty($debug["1"])){
	echo "OK";
	//echo "OK,受影响行数：".$query->rowCount();
	}else{
		echo "系统错误！，错误代码：".$debug["1"]."错误信息：". $debug["2"];
	}
}else{
echo "当前分类下包含留言内容，不可删除！";
}
?>