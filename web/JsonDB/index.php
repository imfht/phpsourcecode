<?php
header("content-Type: text/html; charset=utf-8");
include('JsonDB.class.php');
$db = new JsonDB('area2');
$startTime = microtime(true);
$param=array();
$param['id']=100;
$area=$db->select($param,1);
echo '<pre>';print_r($area);
$endTime = microtime(true);
echo '添加成功，耗时： ' .(($endTime - $startTime)*1000) . 'ms';
?>