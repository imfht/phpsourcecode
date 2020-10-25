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
$video_id=mysqli_real_escape_string($db_link,$_GET['video_id']);
if (empty($video_id)){
    $video_id=mysqli_real_escape_string($db_link,$_POST['video_id']);
}
if (empty($video_id)){
    $result_screenshot=mysqli_query($db_link,"SELECT * FROM screenshot ");
}else{
    $result_screenshot=mysqli_query($db_link,"SELECT * FROM screenshot WHERE video_id = '".$video_id."'");
}
$return['code']="201";
$return['data']['time']=time();
$return['data']['message']="Query Success";
$return['data']['total']=0;
for ($i=0;$row_screenshot=mysqli_fetch_array($result_screenshot);$i++){
    $row_video=mysqli_fetch_array(mysqli_query($db_link,"SELECT * FROM video_list WHERE ID = '".$row_screenshot['video_id']."'"));
    $return['data']['total']++;
    $return['data'][$i]['ID']=$row_screenshot['ID'];
    $return['data'][$i]['video_id']=$row_screenshot['video_id'];
    $return['data'][$i]['type']=$row_screenshot['type'];
    $screenshot_list=json_decode($row_screenshot['files']);
    for ($m=0;$screenshot_list[$m]!="";$m++){
        $return['data'][$i]['files'][$m]="/".$row_video['day']."/".$row_video['random']."/screenshots/".$screenshot_list[$m];
    }
}
echo json_encode($return);
exit;