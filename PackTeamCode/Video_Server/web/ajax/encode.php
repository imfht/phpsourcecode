<?php
include ("../../config/config.php");
include ("../include/function.php");
if ($_GET['action']=="start"){
    $redis=Redis_Link();
    $redis->set('Main_Start','1');
    $data['code']="201";
    echo json_encode($data);
    exit;
}