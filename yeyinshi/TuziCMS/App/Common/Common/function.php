<?php
/*******************************************************************************
* [TuziCMS] 兔子CMS
* @Copyright (C) 2014-2015  http://tuzicms.com   All rights reserved.
* @Team  Yejiao.net
* @Author: 秦大侠 QQ:176881336
* @Licence http://www.tuzicms.com/license.txt
*******************************************************************************/
/** 
 * 验证码检查 
 */
function check_verify($code, $id = 1){  
    $verify = new \Think\Verify();  
    return $verify->check($code, $id);
}

/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
	$p = new Think\Page($count, $pagesize);
	$p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
	$p->setConfig('prev', '上一页');
	$p->setConfig('next', '下一页');
	$p->setConfig('last', '末页');
	$p->setConfig('first', '首页');
	$p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
	$p->lastSuffix = false;//最后一页不显示为总页数
	return $p;
}

/**
* 格式化字节大小
* @param  number $size      字节数
* @param  string $delimiter 数字和单位分隔符
* @return string            格式化后的带单位的大小
 */
function get_byte($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}


/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function get_randomstr($lenth = 6) {
	return get_random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function get_random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}


/**
 * 数据库备份目录
 * @  USER_DATA_PATH在配置文件config中定义
 */
function getDbPath() {
	return C('USER_DATA_PATH'). '/Backupdata';
}

/**
 +----------------------------------------------------------
 * 功能：计算文件大小
 +----------------------------------------------------------
 * @param int $bytes
 +----------------------------------------------------------
 * @return string 转换后的字符串
 +----------------------------------------------------------
 */
function byteFormat($bytes) {
	$sizetext = array(" B", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $sizetext[$i];
}

/**
 * 循环删除目录和文件函数
 * @param string $dirName 路径
 * @param boolean $fileFlag 是否删除目录
 * @return void
 */
function del_dir_file($dirName, $bFlag = false ) {
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					del_dir_file("$dirName/$item", $bFlag);
				} else {
					unlink( "$dirName/$item" );
				}
			}
		}
		closedir( $handle );
		if($bFlag) rmdir($dirName);
	}
}


/**
 * 递归重组信息为多维
 * @param string $dirName 路径
 * @param boolean $fileFlag 是否删除目录
 * @return void
 */
function node_merge($attr, $arr) {
	//$arr=array();
// 	dump($arr);
// 	exit;
	foreach($attr as $v){
		if (is_array($arr)){
			$v['access'] = in_array($v['id'],$arr) ? 1: 0;
		}
	}
	return $attr;
}

/**
 * 验证手机号是否正确
 * @author 范鸿飞
 * @param INT $mobile
 */
function isMobile($mobile) {
	if (!is_numeric($mobile)) {
		return false;
	}
	return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}



/**
 * 获取文件目录列表
 * @param string $pathname 路径
 * @param integer $fileFlag 文件列表 0所有文件列表,1只读文件夹,2是只读文件(不包含文件夹)
 * @param string $pathname 路径
 * @return array
 */
function get_file_folder_List($pathname,$fileFlag = 0, $pattern='*') {
	$fileArray = array();
	$pathname = rtrim($pathname,'/') . '/';
	$list   =   glob($pathname.$pattern);
	foreach ($list  as $i => $file) {
		switch ($fileFlag) {
			case 0:
				$fileArray[]=basename($file);
				break;
			case 1:
				if (is_dir($file)) {
					$fileArray[]=basename($file);
				}
				break;

			case 2:
				if (is_file($file)) {
					$fileArray[]=basename($file);
				}
				break;

			default:
				break;
		}
	}

	if(empty($fileArray)) $fileArray = NULL;
	return $fileArray;
}


/**
 * 截取中文字符串
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
	if(function_exists("mb_substr")){
		$slice= mb_substr($str, $start, $length, $charset);
	}elseif(function_exists('iconv_substr')) {
		$slice= iconv_substr($str,$start,$length,$charset);
	}else{
		$re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
		$re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
		$re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
		$re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	$fix='';
	if(strlen($slice) < strlen($str)){
		$fix='...';
	}
	return $suffix ? $slice.$fix : $slice;
}

/**
 * 反字符 去标签 自动加点 去换行 截取字符串
 */
function cutstr ($data, $no, $le = '') {
	$data = strip_tags(htmlspecialchars_decode($data));
	$data = str_replace(array("\r\n", "\n\n", "\r\r", "\n", "\r"), '', $data);
	$datal = strlen($data);
	$str = msubstr($data, 0, $no);
	$datae = strlen($str);
	if ($datal > $datae)
		$str .= $le;
	return $str;
}


?>