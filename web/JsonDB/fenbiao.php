<?php
header("content-Type: text/html; charset=utf-8");
include('JsonDB.class.php');
$db = new JsonDB();
$startTime = microtime(true);
$array=array();
for($i=1; $i<=100000; $i++){
    $table=$db->getTable('area',$i);
    $array[$table][]=array("id"=>$i,"name"=>"name".$i,"pinyin"=>$i,"pid"=>"0","status"=>"0","sort"=>"0","temp"=>"","letter"=>"\ufeffZ","level"=>"0","region"=>"0");
}
foreach($array as $k=>$v){
    $db->open($k);
    $area=$db->add($v);
}
$endTime = microtime(true);
echo '添加成功，耗时： ' .(($endTime - $startTime)*1000) . 'ms';
?>