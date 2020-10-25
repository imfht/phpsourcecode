<?php
header("Content-Type: text/html; charset=utf-8");
//导入类
require("unit/Validate.php");
require("unit/mysql.php");

//request 参数 安全验证

$email = "";


$key = 'email';

if ( isset($_GET[$key]) ) $email = $_GET[$key];
else if ( isset($_POST[$key]) ) $email= $_POST[$key];
else if ( isset($_COOKIE[$key]) ) $email= $_COOKIE[$key];


$resule['code'] = "success";
$resule['msg'] = "";
if (!Validate::_validataEmail($email)){
	$resule['msg'] = "邮箱错误。";
	$resule['code'] = "faile";

	echo json_encode($resule);
	exit();
}


//连接数据库

$db =  new ConnectionMySQL();

//验证IP地址邮箱

$ip = Validate::GetIP();

$resultdate = $db->fn_select("select id from lotto where email = '$email' or ipaddress = '$ip'");

/*
if(count($resultdate['id'])){
	$resule['msg'] = "您已参与过，谢谢。";
	$resule['code'] = "faile";

	echo json_encode($resule);
	exit();

}
*/

//取得目前库中剩余所有奖品

$rndSum = 10000;
$number =  rand(1,$rndSum);


//取出奖品更新数据库

$prizeString = "";
$noPrize = false;
if ($number==1){
	//手办

	$resultdate = $db->fn_select("select id,prize from lotto where isnull(email) and type = 1 limit 1");

		if (count($resultdate['prize'])){
		$prizeString = $resultdate['prize'];

		$resule['msg'] = "恭喜您获得了《数码宝贝》正版手办";
		$resule['key'] = "正版手办";

		$db->querySqlString("update lotto set email = '$email',ipaddress = '$ip' where id = ".$resultdate['id']);
	}else{
		$noPrize = true;
	}
}

if($number>1 || $noPrize){
	//激活码

	$resultdate = $db->fn_select("select id,prize from lotto where isnull(email) and type = 0 limit 1");

	$prizeString = $resultdate['prize'];

	$resule['msg'] = "恭喜您获得了激活码";
	$resule['key'] = $prizeString;

	$db->querySqlString("update lotto set email = '$email',ipaddress = '$ip' where id = ".$resultdate['id']);

}

$db->close();
$resule['code'] = "success";
echo json_encode($resule);

exit();

