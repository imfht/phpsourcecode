<?php
/*	以下是自动处理的参数，一般不用设置	*/
$info["httpdir"]="http://".$_SERVER['SERVER_NAME'];	//程序所在网址
$info["httpdir"]=$_SERVER["SERVER_PORT"]==80 ? $info["httpdir"] : $info["httpdir"].':'.$_SERVER["SERVER_PORT"];
$baseUrl=str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));	//获取程序所在路径
$info["swtdir"]=empty($baseUrl) ? $info["httpdir"] : $info["httpdir"].'/'.trim($baseUrl,'/');	//处理程序路径
//处理程序路径
$info["zj1"]=substr($info["zj"],0,3);		//专家姓氏
$py=new py_class();		//初始化拼音类
$info["zjpy"]=$py->str2py($info["zj"]);	//专家拼音
$info["dhurl"]="tel:".str_replace("-","",$info["dh"]);	//电话连接(用于手机端)
$info["swturl"]=substr($info["swtid"],0,3).".zoosnet.net";	//商务通域名
@$laiyuanurl=$_SERVER['HTTP_REFERER'];	//上一页网址
if(!isset($laiyuanurl))$laiyuanurl="http://".$_SERVER['SERVER_NAME'];	//未知来源网址
$laiyuanurl=urlencode($laiyuanurl);
//获取第一次来源网址
if(isset($_COOKIE["laiyuanurl"])){
	if(!$_COOKIE["laiyuanurl"]=="")$laiyuanurl=$_COOKIE["laiyuanurl"];
}
?>
