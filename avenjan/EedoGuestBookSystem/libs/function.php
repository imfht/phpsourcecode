<?php
header("Content-type: text/html; charset=utf-8");
require_once 'common.db.php';
//获取系统设置参数
	$system= $database->select("system","*");
	$systeminfo = array();
	foreach($system as $key=>$value){ 
		extract($system[$key]);
		$systeminfo[$name] = $val;
	} 
	foreach($systeminfo as $key => $value){ 
			//$$key=$value; 
			${'system_'.$key}=$value;
	};
//END 获取系统设置参数
//权限控制
function session(){
	global $database;
	$username = $database->count("admin",["uname" => $_COOKIE['username']]);
	if(empty($_COOKIE['username']) || $username < 1){
		header("location:./view/master/page/login/");
		exit;
	};
};

//获取分类列表
function gettypelist(){
	global $database;
	$typelist= $database->select("type","*",["ORDER" => ["id" => "DESC"]]);
	return $typelist;

}
function typelist(){
	global $database;
	$typelist= $database->select("type","*",["ORDER" => ["id" => "DESC"]]);
	if(count($typelist)>0){
		foreach($typelist as $value){ 
		echo '<dd><a href="?type='.$value["id"].'">'.$value["name"].'</a></dd>'; 
		};
	}else{
		echo "暂无分类";
	}
}
function count_tab($name,$b="",$c="",$d=""){
	global $database;
	if(empty($b)){
	$counts = $database->count($name);
	}else{
		$counts = $database->count($name,["$b"."[$c]" => $d]);
	}

	return $counts;
}
function getIp() {
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else
		if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else
			if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
				$ip = getenv("REMOTE_ADDR");
			else
				if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
					$ip = $_SERVER['REMOTE_ADDR'];
				else
					$ip = "unknown";
	return ($ip);
}
function getCity($ip){
$url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
$ip=json_decode(file_get_contents($url));
if((string)$ip->code=='1'){
  return false;
  }
  $data = (array)$ip->data;
return $data;
}
function getclassname($cid){
	global $database;
	$msginfo = $database->select("type", "name", ["id[=]" =>$cid]);
	return $msginfo[0];
}
/*

use:
<?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
?>
*/

?>