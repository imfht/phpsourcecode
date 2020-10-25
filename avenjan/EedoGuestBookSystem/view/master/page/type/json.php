<?php
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
session();
 $list = $database->select("type","*");
 for($i=0;$i<count($list);$i++){
	$num = $database->count("book", ["type[=]" =>$list[$i]['id']]);//统计条数
	$list[$i]['count']=$num;
}
echo json_encode($list);
?>