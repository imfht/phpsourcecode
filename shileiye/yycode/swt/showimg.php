<?php
/*
图片调用文件v150421	By:shileiye
调用方法：<img src="showimg.php?/swt/html/[img|imgs]/swt.gif" />
说明：
1、?后面跟上图片绝对地址。
2、地址中需要切换的文件夹使用[]包括起来，用|隔开。
3、主要用于切换修改一段时间后可能会换回来的图片，如带医院名称的图片。
*/
require 'config.php';		//载入配置文件
//显示哪个文件夹中的图片(从0开始)，如果此数值大于数组的个数，则显示数组中的最后一个值。
@$img=safe_string($_SERVER['QUERY_STRING']);		//获取网址?后边的参数
preg_match("/\[(.*)\]/isU",$img,$dirs);	//获取网址中{xx|xxx}部分
$dir=explode('|',$dirs[1]);		//拆分数组
if(count($dir)<$info["imgwhat"]+1)$info["imgwhat"]=count($dir)-1;
$img=str_replace("[".$dirs[1]."]",$dir[$info["imgwhat"]],$img);		//替换要显示的文件夹
header("Location: ".$img);		//301跳转到地址
?>