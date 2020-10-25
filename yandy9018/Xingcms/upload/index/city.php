<?php

if (!defined('APP_IN')) exit('Access Denied');
if(isset($_GET['cid'])){
setMyCookie("city",intval($_GET['cid']),time()+COOKIETIME );
header("location:".WEB_PATH."index.php");
exit;
}
if(empty($_COOKIE['city']) or $_COOKIE['city']=="undefined"){
$ip = getIp();
$cityname = get_cityname($ip);
if(empty($cityname) or $cityname=="I"){
$cityname="全国";
$cityid = 0;
}else{
$citydata = $db->row_select_one('area',"name='".$cityname."'",'id');
if(!empty($citydata['id'])){
$cityid = $citydata['id'];
}
else{
$cityid = 0;
}
}
setMyCookie("city",$cityid,time()+COOKIETIME);
}
else{
$citydata = $db->row_select_one('area',"id='".$_COOKIE['city']."'",'name');
$cityname = $citydata['name'];
}
echo "document.write(\"".$cityname."\");";
echo "$(function() {
		var title = $(document).attr('title');
		$(document).attr('title', '".$cityname."_'+title);
		})";

?>