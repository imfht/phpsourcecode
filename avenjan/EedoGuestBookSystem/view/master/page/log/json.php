<?php
header("Content-type: text/html; charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
 $list = $database->select("log","*",["ORDER" => ["id" => "DESC"]]);
echo json_encode($list);
?>