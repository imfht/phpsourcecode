<?php

$keyword = $_REQUEST['keyword'];
$number = is_int($_REQUEST['count'])?$_REQUEST['count']:10; //调用条数  最多 10条 默认显示 
$orderty =is_int($_REQUEST['sord'])?$_REQUEST['sord']:1; //1: 按发布时间排序 2:随机排序
if(!file_exists(dirname(__FILE__).'/include/configure/db.php'))
{
	echo "找不到文件";
    exit();
}
require_once(dirname(__FILE__).'/include/configure/db.php');
$conn=mysql_connect('数据库地址','数据库用户名','数据库密码'); //最土数据库配置默认值：'localhost','root','88888888'，请根据自身数据库配置进行修改
if(!$conn){ 
		echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
		exit(0); 
}
$flag=mysql_select_db('数据库名',$conn); //最土数据库名默认值：'zuitu_db'，请根据自身数据库配置进行修改
if(!$flag){ 
		echo"<p align=center>在链接数据库系统数据库里发生了错误,请联系平台客服人员,谢谢</p>"; 
		exit(0); 
}

$keyword = strip_tags($keyword);
if($keyword == ""){
		exit(0);
}
$orderfiled = "begin_time"; 
if ($orderty == 2) {
	$orderfiled = " rand() ";
}
mysql_query("set names utf8");
$select = mysql_query("SELECT * FROM  team where title like '%$keyword%'  order by ".$orderfiled." desc limit ".$number);
$urltitle = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'team.php?id=';
$urlimg = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/static/';
while($group = mysql_fetch_array($select)){
			if($group['title'] == "")break;
			$title = "<Title>".$group['title']."</Title>";//默认调用团购标题，如果感觉微信显示字数过多，建议使用优化标题，将$group['title']修改为$group['seo_title']
			$content = "<Description><![CDATA[".$group['summary']."]]></Description>";
			$url = "<Url>".$urltitle.$group['id']."</Url>";
			$imgurl = "<PicUrl>".$urlimg.$group['image']."</PicUrl>";
			$itmes[] = "<itmes>\n".$title."\n".$url."\n".$imgurl."\n".$content."\n</itmes>";
	}
if($itmes == ""){
	$itmes[] = "<itmes></itmes>";
}
$xml = implode( "\n",$itmes );
$resultStr = '<?xml version="1.0"?>'."\n<feed>\n".$xml."\n</feed>";
echo( $resultStr );					
?>