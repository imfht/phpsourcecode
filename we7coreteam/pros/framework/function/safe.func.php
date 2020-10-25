<?php
/**
 * 提供系统安全获取传入值
 * [WeEngine System] Copyright (c) 2014 W7.CC.
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 从GPC中获取一个数字.
 *
 * @param unknown $value
 * @param string  $default
 */
function safe_gpc_int($value, $default = 0) {
	//如果包含小数点，优先按float对待
	//否则一律按int对待
	if (false !== strpos($value, '.')) {
		$value = floatval($value);
		$default = floatval($default);
	} else {
		$value = intval($value);
		$default = intval($default);
	}

	if (empty($value) && $default != $value) {
		$value = $default;
	}

	return $value;
}

/**
 * 检测某个值是否在某一数组中
 * @param $value 要检测的值
 * @param array $allow 要检测的数组
 * @param string $default 如果$value不在$allow中，返回的默认值
 * @return string
 */
function safe_gpc_belong($value, $allow = array(), $default = '') {
	if (empty($allow)) {
		return $default;
	}
	if (in_array($value, $allow, true)) {
		return $value;
	} else {
		return $default;
	}
}

/**
 * 转换一个安全字符串.
 *
 * @param mixed  $value
 * @param string $default
 *
 * @return string
 */
function safe_gpc_string($value, $default = '') {
	$value = safe_bad_str_replace($value);
	$value = preg_replace('/&((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $value);

	if (empty($value) && $default != $value) {
		$value = $default;
	}

	return $value;
}

/**
 * 转换一个安全路径.
 *
 * @param string $value
 * @param string $default
 *
 * @return string
 */
function safe_gpc_path($value, $default = '') {
	$path = safe_gpc_string($value);
	$path = str_replace(array('..', '..\\', '\\\\', '\\', '..\\\\'), '', $path);

	if (empty($path) || $path != $value) {
		$path = $default;
	}

	return $path;
}

/**
 * 转换一个安全的字符串型数组.
 *
 * @param unknown $value
 * @param array   $default
 */
function safe_gpc_array($value, $default = array()) {
	if (empty($value) || !is_array($value)) {
		return $default;
	}
	foreach ($value as &$row) {
		if (is_numeric($row)) {
			$row = safe_gpc_int($row);
		} elseif (is_array($row)) {
			$row = safe_gpc_array($row, $default);
		} else {
			$row = safe_gpc_string($row);
		}
	}

	return $value;
}

/**
 * 转换一个安全的布尔值
 *
 * @param mixed $value
 *
 * @return boolean
 */
function safe_gpc_boolean($value) {
	return boolval($value);
}

/**
 * 转换一个安全HTML数据.
 */
function safe_gpc_html($value, $default = '') {
	if (empty($value) || !is_string($value)) {
		return $default;
	}
	$value = safe_bad_str_replace($value);

	$value = safe_remove_xss($value);
	if (empty($value) && $value != $default) {
		$value = $default;
	}

	return $value;
}

function safe_gpc_sql($value, $operator = 'ENCODE', $default = '') {
	if (empty($value) || !is_string($value)) {
		return $default;
	}
	$value = trim(strtolower($value));

	$badstr = array(
		'_', '%', "'", chr(39),
		'select', 'join', 'union',
		'where', 'insert', 'delete',
		'update', 'like', 'drop',
		'create', 'modify', 'rename',
		'alter', 'cast',
	);
	$newstr = array(
		'\_', '\%', "''", '&#39;',
		'sel&#101;ct"', 'jo&#105;n', 'un&#105;on',
		'wh&#101;re', 'ins&#101;rt', 'del&#101;te',
		'up&#100;ate', 'lik&#101;', 'dro&#112',
		'cr&#101;ate', 'mod&#105;fy', 'ren&#097;me"',
		'alt&#101;r', 'ca&#115;',
	);

	if ('ENCODE' == $operator) {
		$value = str_replace($badstr, $newstr, $value);
	} else {
		$value = str_replace($newstr, $badstr, $value);
	}

	return $value;
}

/**
 * 转换一个安全URL.
 *
 * @param $_GPC中的值
 * @param bool   $strict_domain 是否严格限制只能为当前域下的URL
 * @param string $default
 */
function safe_gpc_url($value, $strict_domain = true, $default = '') {
	global $_W;
	if (empty($value) || !is_string($value)) {
		return $default;
	}
	$value = urldecode($value);
	if (starts_with($value, './')) {
		return $value;
	}

	if ($strict_domain) {
		if (starts_with($value, $_W['siteroot'])) {
			return $value;
		}

		return $default;
	}

	if (starts_with($value, 'http') || starts_with($value, '//')) {
		return $value;
	}

	return $default;
}

/**
 *  去掉可能造成xss攻击的字符.
 *
 * @param $val $string 需处理的字符串
 */
function safe_remove_xss($val) {
	$val = preg_replace('/([\x0e-\x19])/', '', $val);
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';

	for ($i = 0; $i < strlen($search); ++$i) {
		$val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val);
		$val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val);
	}
	preg_match_all('/href=[\'|\"](.*?)[\'|\"]|src=[\'|\"](.*?)[\'|\"]/i', $val, $matches);
	$url_list = array_merge($matches[1], $matches[2]);
	$encode_url_list = array();
	if (!empty($url_list)) {
		foreach ($url_list as $key => $url) {
			$val = str_replace($url, 'we7_' . $key . '_we7placeholder', $val);
			$encode_url_list[] = $url;
		}
	}
	$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'frameset', 'ilayer', 'bgsound', 'base');
	$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', '@import');
	$ra = array_merge($ra1, $ra2);
	$found = true;
	while (true == $found) {
		$val_before = $val;
		for ($i = 0; $i < sizeof($ra); ++$i) {
			$pattern = '/';
			for ($j = 0; $j < strlen($ra[$i]); ++$j) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[xX]0{0,8}([9ab]);)';
					$pattern .= '|';
					$pattern .= '|(&#0{0,8}([9|10|13]);)';
					$pattern .= ')*';
				}
				$pattern .= $ra[$i][$j];
			}
			$pattern .= '/i';
			$replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2);
			$val = preg_replace($pattern, $replacement, $val);
			if ($val_before == $val) {
				$found = false;
			}
		}
	}
	if (!empty($encode_url_list) && is_array($encode_url_list)) {
		foreach ($encode_url_list as $key => $url) {
			$val = str_replace('we7_' . $key . '_we7placeholder', $url, $val);
		}
	}

	return $val;
}

function safe_bad_str_replace($string) {
	if (empty($string)) {
		return '';
	}
	$badstr = array("\0", '%00', '%3C', '%3E', '<?', '<%', '<?php', '{php', '{if', '{loop', '../');
	$newstr = array('_', '_', '&lt;', '&gt;', '_', '_', '_', '_', '_', '_', '.._');
	$string = str_replace($badstr, $newstr, $string);

	return $string;
}

/**
 * 检测密码强度.
 *
 * @param $password
 *
 * @return array|bool
 */
function safe_check_password($password) {
	$setting = setting_load('register');
	if (!$setting['register']['safe']) {
		return true;
	}
	preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,30}/', $password, $out);
	if (empty($out)) {
		return error(-1, '密码至少8-16个字符，至少1个大写字母，1个小写字母和1个数字，其他可以是任意字符');
	} else {
		return true;
	}
}
