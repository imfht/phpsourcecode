<?php
header("Content-type:text/html;charset=utf-8");
if(!isset($_REQUEST['url']) || !isset($_REQUEST['data'])){
	echo '接口非法调用！';
	die();
}
$url = $_REQUEST['url'];
$data = $_REQUEST['data'];
$token = $_REQUEST['token'];
$time = time();
$random = rand(1,999);
$echostr = rand(1,999).time().rand(1,999);

$url = (strpos($url, "?") ? $url.'&' : $url.'?') . "signature=" . GetSignature( $token, $time, $random ) . "&timestamp=" . $time . "&nonce=" . $random . "&echostr=" . $echostr;

$ch = curl_init();
$header[] = "Content-type: text/xml";//定义content-type为xml
curl_setopt($ch, CURLOPT_URL, $url); //定义表单提交地址
curl_setopt($ch, CURLOPT_POST, 1);   //定义提交类型 1：POST ；0：GET
curl_setopt($ch, CURLOPT_HEADER, 0); //定义是否显示状态头 1：显示 ； 0：不显示
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//定义请求类型
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//定义是否直接输出返回流
curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //定义提交的数据，这里是XML文件
ob_start();
curl_exec($ch);
$result = ob_get_contents();
ob_end_clean();
curl_close($ch);//关闭
echo $result;

//获取signature
function GetSignature($token, $time, $random)
{
	$tmpArr = array($token, $time, $random);
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );
	return $tmpStr;
}