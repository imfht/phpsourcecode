<?php

define('WXACCOUNT', "gh_XXXX");
$keyword = $_REQUEST['keyword'];
$number = is_int($_REQUEST['count'])?$_REQUEST['count']:10; //调用条数  最多 10条 默认显示 
$orderty =is_int($_REQUEST['sord'])?$_REQUEST['sord']:1; //1: 按发布时间排序 2:随机排序


if(!file_exists(dirname(__FILE__).'/data/common.inc.php'))
{
	echo "找不到文件";
    exit();
}


require_once(dirname(__FILE__).'/data/common.inc.php');
$conn=mysql_connect($cfg_dbhost,$cfg_dbuser,$cfg_dbpwd); 
if(!$conn){ 
		echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
		exit(0); 
}
$flag=mysql_select_db($cfg_dbname,$conn); 
if(!$flag){ 
		echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
		exit(0); 
}


$keyword = strip_tags($keyword);
if($keyword == ""){
		exit(0);
}
$orderfiled = "pubdate"; 
if ($orderty == 2) {
	$orderfiled = " rand() ";
}
mysql_query("set names utf8");
$select = mysql_query("SELECT * FROM dede_archives where title like '%$keyword%'  order by ".$orderfiled." desc limit ".$number);
	while($group = mysql_fetch_array($select)){
$url = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/plus/view.php?aid=';
			if($group['title'] == "")break;
			$title = "<Title>".$group['title']."</Title>";
			$content = "<Description><![CDATA[".$group['title']."]]></Description>";
			$url = "<Url>".$url.$group['id']."</Url>";
				$imgurl = "<PicUrl>".$group['litpic']."</PicUrl>";
			$itmes[] = "<itmes>\n".$title."\n".$url."\n".$imgurl."\n".$content."\n</itmes>";
	}
if($itmes == ""){
	$itmes[] = "<itmes></itmes>";
}

$xml = implode( "\n",$itmes );
$resultStr = '<?xml version="1.0"?>'."\n<feed>\n".$xml."\n</feed>";
echo( $resultStr );			

			
?>