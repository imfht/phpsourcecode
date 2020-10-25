<?php


$keyword = $_REQUEST['keyword'];
$number = is_int($_REQUEST['count'])?$_REQUEST['count']:10; //调用条数  最多 10条 默认显示 
$orderty =is_int($_REQUEST['sord'])?$_REQUEST['sord']:1; //1: 按发布时间排序 2:随机排序

if(!is_file('config/config.php')){
	echo "找不到文件";
	exit(0);
}
require_once('config/config.php');
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
		exit(0);
}
$orderfiled = "goods_id"; 
if ($orderty == 2) {
	$orderfiled = "price";
}else if ($orderty == 3) {
	$orderfiled = " rand() ";
}
mysql_query("set names utf8");

$hosturl = "http://".$_SERVER['HTTP_HOST'].'/index.php?product-';
$sql = "SELECT * FROM ".DB_PREFIX."goods where name like '%$keyword%'  order by ".$orderfiled." desc limit ".$number;
$select = mysql_query($sql);
	while($group = mysql_fetch_array($select)){
			if($group['name'] == "")break;
			$title = "<Title>".$group['name']."</Title>";
			$content = "<Description><![CDATA[".$group['name']."]]></Description>";
			$url = "<Url>".$hosturl.$group['goods_id']."</Url>";
			$id  = $group['goods_id'];
			$att  = $group['thumbnail_pic'];
			// $att = mysql_query("SELECT * FROM ".$table_prefix."posts where post_parent='$id' and post_type='attachment' and post_mime_type like '%image%' order by ID desc limit 9");
			if ($att) {
				$a_arr = explode("|", $att);
				$att = "http://".$_SERVER['HTTP_HOST'].'/'.$a_arr[0];
				$imgurl = "<PicUrl><![CDATA[$att]]></PicUrl>";
			}else
				$imgurl = "<PicUrl>http://www.nqtea.com/images/goods/20130910/3ae23178a06e1d07.jpg</PicUrl>";
			$itmes[] = "<itmes>\n".$title."\n".$url."\n".$imgurl."\n".$content."\n</itmes>";
	}
if($itmes == ""){
	$itmes[] = "<itmes></itmes>";
}

$xml = implode( "\n",$itmes );
$resultStr = '<?xml version="1.0"?>'."\n<feed>\n".$xml."\n</feed>";
echo( $resultStr );			

			
?>