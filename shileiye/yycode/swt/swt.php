<?php header('Content-type: application/x-javascript;charset=utf-8');
/*
通用医院网站JS v150505	By:shileiye
调用方法：<script src="/swt/swt.php?strblqwm"></script>
说明：?后面的参数可选
*/
require 'config.php';
@$type = $_SERVER['QUERY_STRING'];			//获取显示类型
if(empty($type))$type=$info["swtnocanshu"];		//设置无参数默认显示项目
if(!preg_match("/^[a-zA-Z\s]+$/",$type))exit();		//传入的参数不是纯英文则停止执行
//判断PC或手机，对不同客户端进行处理
if($info["pcorsj"]=="")$info["pcorsj"]=is_mobile_request()?"sj":"pc";
//PHP数组转JS数组
echo "var info=new Array();\n";
foreach($info as $key=>$value)echo "info['$key']='$value';\n";
HtmlTag("js/other_srcres.js");	//加载外部资源（CSS、JQ等等）
//模版处理
foreach($moban as $key=>$value){
	$mobanvalue=explode('|',$value);
	if(is_int(stripos($type,$key))){
		if(count($mobanvalue)<2){
			HtmlTag($info["swtskins"]."/".$mobanvalue[0]);
		}else{
			if($mobanvalue[1]=="sj" && is_mobile_request()){
				HtmlTag($info["swtskins"]."/".$mobanvalue[0]);
			}elseif($mobanvalue[1]=="pc" && !is_mobile_request()){
				HtmlTag($info["swtskins"]."/".$mobanvalue[0]);
			}elseif($mobanvalue[1]==""){
				HtmlTag($info["swtskins"]."/".$info["pcorsj"]."_".$mobanvalue[0]);
			}
		}
	}
}
//其他处理
HtmlTag("js/other_tongji.js");		//统计及套Q代码
HtmlTag("js/other_onloadfun.js");		//页面载入处理
HtmlTag("js/other_jsfun.js");		//加载js函数库

//自动检查升级程序处理
@$uptimes=file_get_contents("inc/uptimes.txt");
if($uptimes){
	$whatdate=floor((strtotime(date('Y-m-d',time()))-strtotime($uptimes))/86400);
	if($info["autoup"]>0 && $info["autoup"]<=$whatdate){
		echo "document.write('<iframe frameborder=0 scrolling=no src=".$info["swtdir"]."/uplist.php?m=up style=display:none></iframe>');";
	}
}
?>