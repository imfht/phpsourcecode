<?php
include ("../config/config.php");
include ("../web/include/function.php");
//Video Delete API
if (!API_Auth($_GET['key'],$_POST['key'])){
    $return['code']="101";
    $return['time']=time();
    $return['data']['message']="Auth Failed";
    echo json_encode($return);
    exit;
}
$db_link=DB_Link();
$video_id=mysqli_real_escape_string($db_link,$_GET['id']);
$row_video=mysqli_fetch_array(mysqli_query($db_link,"SELECT * FROM video_list WHERE ID = '".$video_id."'"));
if (empty($row_video['ID'])){
    $return['code'] = "101";
    $return['data']['message'] = "Unknown Video";
    echo json_encode($return);
    exit;
}
$result_delete=mysqli_query($db_link,"SELECT * FROM video_list WHERE md5 = '".$row_video['md5']."'");
$return['code'] = "201";
$return['data']['message']="Success Delete ID:";
$return['data']['time']=time();
//删除相同MD5的视频
for ($i=0;$row_delete=mysqli_fetch_array($result_delete);$i++){
    //删除视频记录
    mysqli_query($db_link,"DELETE FROM video_list WHERE ID = '".$row_delete['ID']."'");
    //删除截图文件
    mysqli_query($db_link,"DELETE FROM screenshot WHERE video_id = '".$row_delete['ID']."'");
    $return['data']['message']=$return['data']['message'].$row_delete['ID'];
}
//删除视频文件目录
Delete_Dir("..\\video\\".$row_video['day']."\\".$row_video['random']);
echo json_encode($return);