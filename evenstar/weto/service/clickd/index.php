<?php

/*
 * Copyright (C) xiuno.com
 */

/*
	点击服务器，默认采用 mysql/mongodb 作为存储，如果有独立服务器，建议使用 xiuno clickd.

	GET 参数:
	/?r=1,2,3&w=1,2,3
	
	返回 JSON 格式：
	var xn_json = {"1":"100","2":"101","3":"1000"}
	
*/

// 调试模式: 1 打开，0 关闭
define('DEBUG', 0);

// 应用根目录
define('BBS_PATH', str_replace('\\', '/', substr(__FILE__, 0, -24)));

// check robot
$conf = include BBS_PATH.'conf/conf.php';

define('FRAMEWORK_PATH', BBS_PATH.'xiunophp/');
define('FRAMEWORK_TMP_PATH', $conf['tmp_path']);
define('FRAMEWORK_LOG_PATH', $conf['log_path']);
define('CLICKD_WRITE_MAX', 1); // 一次最大的写入个数

include FRAMEWORK_PATH.'core.php';
core::init();
core::ob_start();

if(misc::is_robot()) {
	header("HTTP/1.0 403 Forbidden");
	exit;
}

$mthread_views = core::model($conf, 'thread_views');

// read
$tids = array();
$r = isset($_GET['r']) ? $_GET['r'] : '';
$r && $tids = explode(',', $r);

// 过滤
foreach($tids as &$_tid) {
	$_tid = intval($_tid);
}
$tids = array_filter($tids);
if(count($tids) > 100) {
	$tids = array_slice($tids, 0, 100);
}

if(!empty($tids)) {
	$arr = $mthread_views->mget($tids);
	$s = '';
	foreach($arr as $k=>$v) {
		if(empty($v)) {
			list($_, $_, $tid) = explode('-', $k);
			$tid = intval($tid);
			$s .= ',"'.$tid.'":'.'"0"';
		} else {
			$s .= ',"'.$v['tid'].'":'.'"'.$v['views'].'"';
		}
	}
	$s = '{'.substr($s, 1).'}';
	$s = "var xn_json = $s;";
	echo $s;
} else {
	echo "var xn_json = {};";
}

$tids = array();
$w = isset($_GET['w']) ? $_GET['w'] : '';
$w && $tids = explode(',', $w);
foreach($tids as &$_tid) {
	$_tid = intval($_tid);
}
if(!empty($tids)) {
	$tids = array_slice($tids, 0, CLICKD_WRITE_MAX); // 只允许写一个
	$arr = $mthread_views->mget($tids);
	foreach($arr as $k=>$v) {
		if(empty($v)) {
			list($_, $_, $tid) = explode('-', $k);
			$v = array('tid'=>$tid, 'views'=>1);
			$mthread_views->create($v);
		} else {
			$v['views']++;
			$mthread_views->update($v);
		}
	}
}

?>