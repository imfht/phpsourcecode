<?php

/**
 * 批量定义常量
 * @param type $config
 */
function setConfig($config) {
	if (is_array($config)) {
		foreach ($config as $key => $value) {
			define($key, $value);
		}
	} else {
		return false;
	}
}

/**
 * 返回定义的常量
 * @param type $name
 * @return type
 */
function getConfig($name) {
	return strtoupper($name);
}

/**
 * 检测是否为post 请求
 * @return boolean
 */
function isPost() {
	if (isset($_POST["isPost"]) && $_POST["isPost"] == 1) {
		return true;
	} else {
		return false;
	}
}

/**
 * 创建url
 * @param type $string
 * @return type
 */
function URL($string, $params = array()) {
	$str = "";
	if (empty($params)) {
		return WEBROOT . "/" . $string;
	} else {
		foreach ($params as $key => $value) {
			$str.=$key . "=" . $value;
		}
		return WEBROOT . "/" . $string . "?" . $str;
	}
}
/**
 * 自动生成下拉框
 * @param type $id
 * @param type $data
 * @param type $seleted
 * @return boolean|string
 */
function tlpSelet($name,$data,$seleted,$id=""){
	if(is_array($data)){
		$str="<select id='$id' name='$name'>";
		$str.="<option value=''>全部</option>";
		foreach ($data as $key => $value) {
			if($seleted===$key){
				 $str.="<option value ='$key' selected = 'selected' >$value</option>";
			}else{
				 $str.="<option value ='$key'>$value</option>";
			}
		}
		$str.="</select>";
		return $str;
	}else{
		return false;
	}
}
/**
 * 数组转化
 * @param type $list
 * @return type
 */
function changeArray($list){
	foreach ($list as $key => $value) {
		$result[$value["id"]]=$value["username"];
	}
	return $result;
}
/**
 * 计算中文字符串长度
 * @param string $string
 * @return type
 */
function utf8_strlen($string = null) {
	// 将字符串分解为单元
	preg_match_all("/./us", $string, $match);
	// 返回单元个数
	return count($match[0]);
}
/**
 * 获取真实ip
 * @return type
 */
function get_real_ipaddress() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		return $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	return $_SERVER['REMOTE_ADDR'];
}
/**
 * 判断ip是否在某一IP段内
 * @param type $ip
 * @param type $ip_one
 * @param type $ip_two
 * @return type
 */
function in_ip_range($ip, $ip_one, $ip_two = false) {
	if (!$ip_two) {
		return $ip_one === $ip;
	}
	return ip2long($ip_one) * -1 >= ip2long($ip) * -1 && ip2long($ip_two) * -1 <= ip2long($ip) * -1;
}

/**
 * 获取get post 数据并进行转义
 * @param type $name
 * @return type
 */
function getRequest($name,$default=0,$is_int=false){
	if(isset($_GET[$name])&&$_GET[$name]){
		if($is_int){
			return is_int((int)$_GET[$name]) ? $_GET[$name] : $default;
		}else{
			return addslashes($_GET[$name]);
		}
	}
	if(isset($_POST[$name])&&$_POST[$name]){
		if($is_int){
			return is_int((int)$_POST[$name]) ? $_POST[$name] : $default;
		}else{
			return addslashes($_POST[$name]);
		}
	}
	return $default;
}

function includeTemplate($path,$values=[]){
	extract($values);
	include WEBPATH.'/apps/templates/'.$path;
}
/**
 * 输出json
 * @param type $data
 */
function jsonReturn($data) {
	echo json_encode($data);
	exit;
}
