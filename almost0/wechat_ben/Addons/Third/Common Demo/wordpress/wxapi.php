<?php

define('WXACCOUNT', "gh_XXXXX");
$keyword = $_REQUEST['keyword'];
$number = is_int($_REQUEST['count'])?$_REQUEST['count']:10; //调用条数  最多 10条 默认显示 
$orderty =$_REQUEST['sord']; //1: 按发布时间排序 2: 按评论数排序 3:随机排序

if(!is_file('wp-config.php')){
	echo "找不到文件";
	exit(0);
}
require_once('wp-config.php');
$conn=mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
if(!$conn){ 
	echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
	exit(0); 
}
$flag=mysql_select_db(DB_NAME,$conn); 
if(!$flag){ 
		echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
		exit(0); 
}


$keyword = strip_tags($keyword);
if($keyword == ""){
		//exit(0);
}
$orderfiled = "post_date"; 
if ($orderty == 2) {
	$orderfiled = "comment_count";
}else if ($orderty == 3) {
	$orderfiled = " rand() ";
}
else {
$orderfiled = "post_date"; 
}
mysql_query("set names utf8");


$select = mysql_query("SELECT * FROM ".$table_prefix."posts where post_title like '%$keyword%' and post_status='publish' and (post_type='post' or post_type='page') order by ".$orderfiled." desc limit ".$number);
	while($group = mysql_fetch_array($select)){
			if($group['post_title'] == "")break;
			$title = "<Title>".$group['post_title']."</Title>";
			$content = "<Description><![CDATA[".$group['post_content']."]]></Description>";
			$url = "<Url><![CDATA[".$group['guid']."]]></Url>";
			$id  = $group['ID'];
			$att = mysql_query("SELECT * FROM ".$table_prefix."posts where post_parent='$id' and post_type='attachment' and post_mime_type like '%image%' order by ID desc limit 9");
			if ($att) {
				$img = mysql_fetch_array($att);
				$imgurl = "<PicUrl><![CDATA[$img[guid]]]></PicUrl>";
			}else
				$imgurl = "<PicUrl></PicUrl>";
			$itmes[] = "<itmes>\n".$title."\n".$url."\n".$imgurl."\n".$content."\n</itmes>";
	}
if($itmes == ""){
	$itmes[] = "<itmes></itmes>";
}

$xml = implode( "\n",$itmes );
$resultStr = '<?xml version="1.0"?>'."\n<feed>\n".$xml."\n</feed>";
echo( $resultStr );			

			
?>