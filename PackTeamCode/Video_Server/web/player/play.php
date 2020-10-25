<?php
include("../../config/config.php");
include("../include/function.php");
if (!Login_Status()) {
    header("Location:login.php");
    exit;
}
$video_id=$_GET['id'];
if (empty($video_id)){
    echo "Empty Video ID";
    exit;
}
$db_link=DB_Link();
$video_id=mysqli_real_escape_string($db_link,$video_id);
$row_video=mysqli_fetch_array(mysqli_query($db_link,"SELECT * FROM video_list WHERE ID = '".$video_id."'"));
if (empty($row_video['ID'])){
    echo "Error Video ID";
    exit;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Player</title>
    <link href="css/video-js.css" rel="stylesheet">
</head>
<body>
<div style="width: 100%;">
    <video id="player" width="1280" height="720" class="video-js vjs-default-skin" controls>
        <source src="http://<?php echo Get_Config('video_domain');?>:<?php echo Get_Config('video_port');?>/<?php echo $row_video['day'];?>/<?php echo $row_video['random'];?>/index.m3u8" type="application/x-mpegURL">
    </video>
</div>
<h3>JPEG File</h3>
<?php
$row_jpeg=mysqli_fetch_array(mysqli_query($db_link,"SELECT * FROM screenshot WHERE video_id = '".$video_id."' AND type = '1'"));
$jpeg_file=json_decode($row_jpeg['files'],1);
for ($i=0;!empty($jpeg_file[$i]);$i++){
    ?>
    <img src="http://<?php echo Get_Config('video_domain');?>:<?php echo Get_Config('video_port');?>/<?php echo $row_video['day'];?>/<?php echo $row_video['random'];?>/screenshots/<?php echo $jpeg_file[$i];?>">
<?php
}
?>
<h3>GIF File</h3>
<?php
$row_gif=mysqli_fetch_array(mysqli_query($db_link,"SELECT * FROM screenshot WHERE video_id = '".$video_id."' AND type = '2'"));
$gif_file=json_decode($row_gif['files'],1);
for ($i=0;!empty($gif_file[$i]);$i++){
    ?>
    <img src="http://<?php echo Get_Config('video_domain');?>:<?php echo Get_Config('video_port');?>/<?php echo $row_video['day'];?>/<?php echo $row_video['random'];?>/screenshots/<?php echo $gif_file[$i];?>">
    <?php
}
?>
</body>
<script src="js/video.js"></script>
<script src="js/videojs-contrib-hls.js"></script>
<script>
    var player = videojs('player');
</script>
</html>
