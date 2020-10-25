<?php

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env(){
	$items = array(
		'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
		'php'     => array('PHP版本', '5.3', '5.3+', PHP_VERSION, 'success'),
		'upload'  => array('附件上传', '不限制', '2M+', '未知', 'success'),
		'gd'      => array('GD库', '2.0', '2.0+', '未知', 'success'),
	);

	//PHP环境检测
	if($items['php'][3] < $items['php'][1]){
		$items['php'][4] = 'error';
	}

	//附件上传检测
	if(@ini_get('file_uploads'))
		$items['upload'][3] = ini_get('upload_max_filesize');

	//GD库检测
	$tmp = function_exists('gd_info') ? gd_info() : array();
	if(empty($tmp['GD Version'])){
		$items['gd'][3] = '未安装';
		$items['gd'][4] = 'error';
	} else {
		$items['gd'][3] = $tmp['GD Version'];
	}
	unset($tmp);
	return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile(){
	$items = array(
		array('dir',  '可写', 'success', ROOT_PATH.'upload'),
		array('dir',  '可写', 'success', ROOT_PATH.'data'),
	);
	foreach ($items as &$val) {
		if('dir' == $val[0]){
			if(!is_writable($val[3])) {
				if(is_dir($items[3])) {
					$val[1] = '可读';
					$val[2] = 'error';
					session('error', true);
				} else {
					$val[1] = '不存在';
					$val[2] = 'error';
					session('error', true);
				}
			}
		} else {
			if(file_exists($val[3])) {
				if(!is_writable($val[3])) {
					$val[1] = '不可写';
					$val[2] = 'error';
					session('error', true);
				}
			} else {
				if(!is_writable(dirname($val[3]))) {
					$val[1] = '不存在';
					$val[2] = 'error';
					session('error', true);
				}
			}
		}
	}
	return $items;
}

/**
 * 函数检测
 * @return array 检测数据
 */
function check_func(){
	$items = array(
		array('mysql',     '支持', 'success'),
		array('mysqlpdo',     '支持', 'success'),
		array('file_get_contents', '支持', 'success'),
		array('mb_strlen',		   '支持', 'success'),
		array('eval',		   '支持', 'success'),
		array('pathinfo',		   '支持', 'success'),
	);

	foreach ($items as &$val) {
		if(!function_exists($val[0])){
			$val[1] = '不支持';
			$val[2] = 'error';
			session('error', true);
		}
	}
	return $items;
}

/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = true){
	if($class){
		echo "<script type=\"text/javascript\">showmsg(\"{$msg}\")</script>";
	}else{
		echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"error\")</script>";
		exit;
	}
}