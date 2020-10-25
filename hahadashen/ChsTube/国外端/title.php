<?php
error_reporting(0);
//国外端
session_start();
$videoid=$_GET['id'];
function youtube_title($id) {
// $id = 'YOUTUBE_ID';
// returns a single line of JSON that contains the video title. Not a giant request.
$videoTitle = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=".$id."&key=AIzaSyDl6JIGXroKbxsqE9AZkeUMAHRDY6qk_5o&fields=items(id,snippet(title),statistics)&part=snippet,statistics");
// despite @ suppress, it will be false if it fails
if ($videoTitle) {
$json = json_decode($videoTitle, true);

return $json['items'][0]['snippet']['title'];
} else {
return false;
}
}
$title = youtube_title($videoid);
echo $title;
?>