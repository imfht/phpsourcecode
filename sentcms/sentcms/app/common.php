<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
// 应用公共文件
use app\model\Member;
use think\facade\Request;
use think\facade\Session;
use think\facade\Config;

/**
 * @title 检查登录状态
 */
function is_login() {
	if(strpos(Request::controller(), ".")){
		list($module, $controller) = explode(".", Request::controller());
	}else{
		$module = "";
	}
	$user = Session::get(strtolower($module) . 'Info');
	return isset($user['uid']) ? $user['uid'] : false;
}

/**
 * @title 检查是否为超级管理员
 */
function is_administrator() {
	if(strpos(Request::controller(), ".")){
		list($module, $controller) = explode(".", Request::controller());
	}else{
		$module = "";
	}
	$user = Session::get(strtolower($module) . 'Info');
	return (int) $user['uid'] === (int) env('rootuid') ? true : false;
}

function form($field = [], $data = []) {
	return \app\http\form\Form::render($field, $data);
}

/**
 * 广告位广告
 * @param string $name 广告位名称
 * @param array $param 参数
 * @return mixed
 */
function ad($name, $param = []) {
	return '';
}

function parse_field_bind() {

}

function time_format($value) {
	return date('Y-m-d H:i:s', $value);
}

// 不区分大小写的in_array实现
function in_array_case($value, $array) {
	return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false) {
	$type = $type ? 1 : 0;
	static $ip = NULL;
	if ($ip !== NULL) {
		return $ip[$type];
	}

	if ($adv) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos = array_search('unknown', $arr);
			if (false !== $pos) {
				unset($arr[$pos]);
			}

			$ip = trim($arr[0]);
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	} elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u", ip2long($ip));
	$ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr")) {
		$slice = mb_substr($str, $start, $length, $charset);
	} elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
		if (false === $slice) {
			$slice = '';
		}
	} else {
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	if (strlen($slice) == strlen($str)) {
		return $slice;
	} else {
		return $suffix ? $slice . '...' : $slice;
	}
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {
		//获取当前登录用户名
		return session('userInfo.username');
	}
	$name = Member::where('uid', $uid)->value('username');
	return $name;
}

/**
 * 根据用户ID获取昵称
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_nickname($uid = 0) {
	static $list;
	if (!($uid && is_numeric($uid))) {
		//获取当前登录用户名
		return session('userInfo.nickname');
	}
	$name = Member::where('uid', $uid)->value('nickname');
	return $name ? $name : '未知';
}

function avatar($uid, $size = 'middle') {
	return request()->domain() . '/static/common/images/default_avatar_' . $size . '.jpg';
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
	$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
	if (strpos($string, ':')) {
		$value = array();
		foreach ($array as $val) {
			list($k, $v) = explode(':', $val);
			$value[$k] = $v;
		}
	} else {
		$value = $array;
	}
	return $value;
}

function mk_dir($dir, $mode = 0755) {
	if (is_dir($dir) || @mkdir($dir, $mode, true)) {
		return true;
	}

	if (!mk_dir(dirname($dir), $mode, true)) {
		return false;
	}

	return @mkdir($dir, $mode, true);
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str = '', $glue = ',') {
	if ($str) {
		return explode($glue, $str);
	} else {
		return array();
	}
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr = array(), $glue = ',') {
	if (empty($arr)) {
		return '';
	} else {
		return implode($glue, $arr);
	}
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) {
		$size /= 1024;
	}

	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 获取附件信息
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 */
function get_attach($id, $field = false, $is_list = false) {
	$basePath = request()->domain();
	if (empty($id)) {
		return $basePath . '/static/common/images/default.png';
	}
	if (false !== strpos($id, ",") || $is_list) {
		$map[] = ['id', 'IN', explode(",", $id)];
		$picture = \app\model\Attach::where($map)->column("*", "id");
		return $picture;
	} else {
		$map[] = ['id', '=', $id];
		$picture = \app\model\Attach::where($map)->find();
		$picture['url'] = $basePath . $picture['url'];
		if ($field == 'path') {
			if ($picture['path'] == '') {
				$picture['path'] = $picture['url'] ? $picture['url'] : $basePath . '/static/common/images/default.png';
			} else {
				$picture['path'] = $picture['path'] ? $basePath . $picture['path'] : $basePath . '/static/common/images/default.png';
			}
		}
		return (false !== $field) ? $picture[$field] : $picture;
	}
}