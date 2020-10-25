<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
//下载函数
/*
$filename		下载文件地址或路径
*/
function download($filename){
	if(preg_match("/^http\:\/\//i",$filename)){
		header("location:$filename"); 
	}else{
		header('Content-Description: File Transfer'); 
		header('Content-Type: application/octet-stream'); 
		$simplename = basename($filename);
		header('Content-Disposition: attachment; filename='.$simplename); 
		header('Content-Transfer-Encoding: binary'); 
		header('Expires: 0'); 
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
		header('Pragma: public'); 
		header('Content-Length: ' . filesize($filename)); 
		ob_clean(); 
		flush(); 
		readfile($filename); 
	}	
}