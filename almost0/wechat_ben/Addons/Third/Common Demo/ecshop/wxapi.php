<?php
/*
微商号，微信运营与营销服务平台
Powered by www.weishanghao.com    
*/
define('WXACCOUNT', "gh_xxxxxxxxxxxx");//原始ID：成功接入微商号平台后，请将 gh_xxxxxxxxxxxx 更换成您自己的原始ID
$number = 3;  //调用条数：最多默认显示调用10条商品数据
$orderty = 1; //商品排序：1：按发布时间排序，2：随机排序
if(!file_exists(dirname(__FILE__).'/data/config.php'))
{
	echo "找不到文件";
    exit();
}
require_once(dirname(__FILE__).'/data/config.php');
$conn=mysql_connect($db_host,$db_user,$db_pass); 
if(!$conn){ 
		echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
		exit(0); 
}
$flag=mysql_select_db($db_name,$conn); 
if(!$flag){ 
		echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
		exit(0); 
}
$keyword = $_REQUEST['keyword'];

$keyword = strip_tags($keyword);
if($keyword == ""){
		exit(0);
}
$orderfiled = "add_time"; 
if ($orderty == 2) {
	$orderfiled = " rand() ";
}
mysql_query("set names utf8");
$select = mysql_query("SELECT * FROM ecs_goods  where goods_name like '%$keyword%'  order by ".$orderfiled." desc limit ".$number);
$urltitle = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'goods.php?id=';
$urlimg = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/';
while($group = mysql_fetch_array($select)){
			if($group['goods_name'] == "")break;
			$title = "<Title>".$group['goods_name']."</Title>";
			$content = "<Description><![CDATA[".$group['goods_desc']."]]></Description>";
			$url = "<Url>".$urltitle.$group['goods_id']."</Url>";
			$imgurl = "<PicUrl>".$urlimg.$group['original_img']."</PicUrl>";
			$itmes[] = "<itmes>\n".$title."\n".$url."\n".$imgurl."\n".$content."\n</itmes>";
	}
if($itmes == ""){
	$itmes[] = "<itmes></itmes>";
}
$xml = implode( "\n",$itmes );
$resultStr = '<?xml version="1.0"?>'."\n<feed>\n".$xml."\n</feed>";
echo( $resultStr );			
			
?>