<?php

define('SYS_FINANCE_URL', 'http://zb.hiici.com');
define('SYS_FORUM_URL', 'http://www.hiici.com');
define('SYS_NAME', '搜索网');
define('SYS_EN_NAME', 'hiici.com');
global $config;
if ($config['OSS_ACCESS_ID']) define('FACE_URL', 'http://'.$config['OSS_URL'].'/user_face-'); else define('FACE_URL', 'img/center/user_face/');

ob_start();
ini_set('session.gc_maxlifetime', 30*24*3600); 
session_start();

require_once('safe.php');		//安全模块
require_once('anti_word.php');		//垃圾词检查
require_once('dt.php');			//数据库模块
require_once('func.php');		//通用函数

//构造控制器文件
$f_c_and_a = null; //控制文件
$f_c_css = null; //控制器css文件
$f_c_js = null; //控制器js文件
$f_c_pub_h = null; //控制器公共头phtml文件
$f_c_pub_f = null; //控制器公共尾phtml文件
get_c_and_a();
function get_c_and_a() {
	global $f_c_and_a;
	global $f_c_css;
	global $f_c_js;
	global $f_c_pub_h;
	global $f_c_pub_f;

	//获取控制器
	if (empty($_GET['c'])) {
		$c = 'index';
	} else {
		$c = filter_var($_GET['c'], FILTER_SANITIZE_STRING);
		$c = empty($c) ? 'index' : $c;
	}

	//获取行为
	if (empty($_GET['a'])) {
		$a = 'index';
	} else {
		$a = filter_var($_GET['a'], FILTER_SANITIZE_STRING);
		$a = empty($a) ? 'index' : $a;
	}

	//如果为php控制文件，执行，然后结束脚本
	$f_php = $c.'/'.$a.'.php';
	if (file_exists($f_php)) {
		require_once($f_php);
		die();
	}

	//保存phtml控制器文件名
	$f_c_and_a = $c.'/'.$a.'.phtml';
	$f_c_css = $c.'/inc/'.$c.'.css';
	$f_c_js = $c.'/inc/'.$c.'.js';
	$f_c_pub_h = $c.'/inc/pub_h.phtml';
	$f_c_pub_f = $c.'/inc/pub_f.phtml';
}

//包含控制器文件来输出内容
function print_content() {
	global $f_c_and_a;
	global $f_c_css;
	global $f_c_js;
	global $f_c_pub_h;
	global $f_c_pub_f;

	if (file_exists($f_c_and_a)) {
		//如果有css文件，打印link
		if (file_exists($f_c_css)) print '<link rel="stylesheet" href="'.$f_c_css.'">'; 
		//如果有js文件，打印link
		if (file_exists($f_c_js)) print '<script src="'.$f_c_js.'"></script>'; 
		if (file_exists($f_c_pub_h)) require_once($f_c_pub_h);
		require_once($f_c_and_a);
		if (file_exists($f_c_pub_f)) require_once($f_c_pub_f);
	} else {
		require_once('index/index.phtml');
	}
}
