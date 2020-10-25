<?php

function getSex($sex) {
	switch ($sex) {
		case \CigoAdminLib\Lib\Admin::SEX_MAN:
			$sexTxt = '男';
			break;
		case \CigoAdminLib\Lib\Admin::SEX_WOMEN:
			$sexTxt = '女';
			break;
		case \CigoAdminLib\Lib\Admin::SEX_UNKOWN:
		default:
			$sexTxt = '保密';
			break;
	}
	return $sexTxt;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string $name 格式 [模块名]/接口名/方法名
 * @param  array|string $vars 参数
 * @return mixed
 */
function api($name, $vars = array()) {
	$array = explode('/', $name);
	$method = array_pop($array);
	$classname = array_pop($array);
	$module = $array ? array_pop($array) : 'Common';
	$callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
	if (is_string($vars)) {
		parse_str($vars, $vars);
	}

	return call_user_func_array($callback, $vars);
}

function get_menu_url($url) {
	switch ($url) {
		case 'http://' === substr($url, 0, 7):
		case '#' === substr($url, 0, 1):
			break;
		default:
			$url = U($url);
			break;
	}
	return $url;
}

/**
 * 检查终端类型
 * @return int
 */
function checkClientType() {
	$clientAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if (strpos($clientAgent, "android")) {
		return \CigoAdminLib\Lib\Admin::CLIEANT_TYPE_ANDROID;
	} else if (strpos($clientAgent, "iphone")) {
		return \CigoAdminLib\Lib\Admin::CLIEANT_TYPE_IPHONE;
	} else {
		return \CigoAdminLib\Lib\Admin::CLIEANT_TYPE_PC;
	}
}

/**
 * 检查是否微信终端
 */
function checkIfWeiXinClientType() {
	$clientAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
	return strpos($clientAgent, "micromessenger");
}

/**
 * 将从数据库中读取的文案数据转化成前端显示数据
 *
 * @param $dbSrc
 *
 * @return string
 */
function convert_doc_content($dbSrc) {
	return htmlspecialchars_decode(html_entity_decode($dbSrc));
}

/**
 * 获取上传文件路径
 * @param int $fileId
 * @param string $field
 * @param string $model
 * @return string
 */
function getUploadFilePath($fileId = 0, $field = 'path', $model = "Files") {
	if (empty($fileId)) {
		return '';
	}
	$data = M($model)->where(array('status' => 1))->getById($fileId);
	if (!$data) {
		return '';
	}
	return empty($field) ? $data : trim($data[$field], '.');
}

/**
 * 格式验证
 * @param $value
 * @param $rule
 * @return bool
 */
function regex($value, $rule) {
	$validate = array(
		'username' => '/^\w{3,20}$/',
		'password' => '/^[A-Za-z0-9_]{6,20}$/',
		'nickname' => '/^\w{1,6}$/',
		'phone' => '/^1[134578]{1}[0-9]{9}$/',
		'require' => '/\S+/',
		'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
		'url' => '/^http(s?):\/\/(?:[A-Za-z0-9-_]+\.)+[A-Za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
		'currency' => '/^\d+(\.\d+)?$/',
		'number' => '/^\d+$/',
		'zip' => '/^\d{6}$/',
		'integer' => '/^[-\+]?\d+$/',
		'double' => '/^[-\+]?\d+(\.\d+)?$/',
		'english' => '/^[A-Za-z]+$/',
	);
	// 检查是否有内置的正则表达式
	if (isset($validate[strtolower($rule)]))
		$rule = $validate[strtolower($rule)];

	return preg_match($rule, $value) === 1;
}

/**
 * 手机号码格式判断
 * @param string $phone
 * @return bool
 */
function formatCheckPhone($phone = '') {
	return regex($phone, 'phone');
}

/**
 * 密码格式判断
 * @param string $password
 * @return bool
 */
function formatCheckPassword($password = '') {
	return regex($password, 'password');
}

/**
 * 昵称格式判断
 * @param string $nickName
 * @return bool
 */
function formatCheckNickName($nickName = '') {
	return regex($nickName, 'nickname');
}

/**
 * 数字格式判断
 * @param int $int
 * @return bool
 */
function formatCheckInteger($int = 0) {
	return regex($int, 'integer');
}


/**
 * 邮箱格式判断
 * @param string $email
 * @return bool
 */
function formatCheckEmail($email = '') {
	return regex($email, 'email');
}

/**
 * 时间戳格式化
 * @param int $time
 * @param string $format 时间格式
 * @return string 完整的时间显示
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
	$time = $time === NULL ? NOW_TIME : intval($time);
	return date($format, $time);
}

/**
 * 检测验证码
 *
 * @param $code 验证码
 * @param integer $id 验证码ID
 * @return bool 检测结果
 */
function check_verify($code, $id = 0) {
	$verify = new \Think\Verify();

	return $verify->check($code, $id);
}

function encryptUserPwd($password) {
	return base64_encode(hash('sha256', $password, false));
}


/**
 * 系统加密方法
 *
 * @param string $data
 *            要加密的字符串
 * @param string $key
 *            加密密钥
 * @param int $expire
 *            过期时间 (单位:秒)
 *
 * @return string
 */
function ucenter_encrypt($data, $key = 'asdfasd#sdfjJJ134@sdjf^%', $expire = 0) {
	$key = md5($key);
	$data = base64_encode($data);
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}
		$char .= substr($key, $x, 1);
		$x++;
	}
	$str = sprintf('%010d', $expire ? $expire + time() : 0);
	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
	}

	return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 *
 * @param string $data
 *            要解密的字符串 （必须是ucenter_encrypt方法加密的字符串）
 * @param string $key
 *            加密密钥
 *
 * @return string
 */
function ucenter_decrypt($data, $key = 'asdfasd#sdfjJJ134@sdjf^%') {
	$key = md5($key);
	$x = 0;
	$data = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data = substr($data, 10);
	if ($expire > 0 && $expire < time()) {
		return '';
	}
	$len = strlen($data);
	$l = strlen($key);
	$char = $str = '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) {
			$x = 0;
		}
		$char .= substr($key, $x, 1);
		$x++;
	}
	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		} else {
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}

	return base64_decode($str);
}

function https_request($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_URL, $url);
	$res = curl_exec($curl);
	curl_close($curl);
	return $res;
}
