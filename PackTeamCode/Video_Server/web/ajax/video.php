<?php
include("../../config/config.php");
include("../include/function.php");
if ($_GET['action']=="delete"){
    if (!Login_Status()) {
        $return['code'] = "101";
        $return['data']['message'] = "Login Status Error!";
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
    $return['data']['message']="Success Delete ID:";
    //删除相同MD5的视频
    for ($i=0;$row_delete=mysqli_fetch_array($result_delete);$i++){
        //删除视频记录
        mysqli_query($db_link,"DELETE FROM video_list WHERE ID = '".$row_delete['ID']."'");
        //删除截图文件
        mysqli_query($db_link,"DELETE FROM screenshot WHERE video_id = '".$row_delete['ID']."'");
        $return['data']['message']=$return['data']['message'].$row_delete['ID'];
    }
    //删除视频文件目录
    Delete_Dir("..\\..\\video\\".$row_video['day']."\\".$row_video['random']);
    $return['code'] = "201";
    echo json_encode($return);
    exit;
}elseif($_GET['action']=="video_list"){
    if (!Login_Status()) {
        $return['code'] = "101";
        $return['data']['message'] = "Login Status Error!";
        echo json_encode($return);
        exit;
    }
    $db_link=DB_Link();
    //检查传入参数
    $page=mysqli_real_escape_string($db_link,$_GET['page']);
    $num=mysqli_real_escape_string($db_link,$_GET['num']);
    if (empty($num)){
        $num=20;
    }
    $result_video=mysqli_query($db_link,"SELECT * FROM video_list ORDER BY ID DESC");
    $num_video=mysqli_num_rows($result_video);
    $start_row=$page-1;
    $start_row=$start_row*$num;
    $end_row=$start_row+$num;
    //限制查询位数 防止出现错误
    if ($start_row>$num_video){
        $start_row=$num_video;
    }
    if ($end_row>$num_video){
        $end_row=$num_video;
    }
    //计算总页数
    $total_page=ceil($num_video/$num);
    //整理查询数据
    $return['code']=201;
    $return['data']['total_page']=$total_page;
    for ($m=$i=0;$i<$end_row;$i++){
        $row_video=mysqli_fetch_array($result_video);
        if ($i>=$start_row&&$i<=$end_row){
            //写入输出缓存
            $return['data'][$m]['ID']=$row_video['ID'];
            $return['data'][$m]['filename']=$row_video['filename'];
            $return['data'][$m]['format_time']=date('Y-m-d H:i:s',$row_video['time']);
            $return['data'][$m]['m3u8_link']='http://'.Get_Config('video_domain').':'.Get_Config('video_port').'/'.$row_video['day'].'/'.$row_video['random'].'/index.m3u8';
            $return['data'][$m]['status']=$row_video['status'];
            $m++;
        }
    }
    echo json_encode($return);
    exit;
}