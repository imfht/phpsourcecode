<?php
include ("../config/config.php");
include ("../web/include/function.php");
//Video List API
if (!API_Auth($_GET['key'],$_POST['key'])){
    $return['code']="101";
    $return['time']=time();
    $return['data']['message']="Auth Failed";
    echo json_encode($return);
    exit;
}
$db_link=DB_Link();
$result_video=mysqli_query($db_link,"SELECT * FROM video_list ORDER BY ID DESC ");
$return['code']="201";
$return['data']['time']=time();
$return['data']['message']="Query Success";
$return['data']['total']=0;
for ($i=0;$row_video=mysqli_fetch_array($result_video);$i++){
    $return['data']['total']++;
    $return['data'][$i]['ID']=$row_video['ID'];
    $return['data'][$i]['filename']=$row_video['filename'];
    $return['data'][$i]['m3u8']="/".$row_video['day']."/".$row_video['random']."/index.m3u8";
    $return['data'][$i]['time']=$row_video['time'];
    $return['data'][$i]['status']=$row_video['status'];
}
echo json_encode($return);