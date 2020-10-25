<?php
$ext   = extendName($request['filename']);
$file  = ROOTPATH.UPLOADPATH.$request['path'].$request['filename'];
switch($ext){
	 case 'jpg': 
		 include 'factory/pic.php';
		 break;
	case 'png': 
		 include 'factory/pic.php';
		 break;
	case 'gif': 
		 include 'factory/pic.php';
		 break;
	case 'bmp': 
		 include 'factory/pic.php';
		 break;
	 default:
	 	/*
	 	 *谷歌文件查看器
	 	 */
	 	$file="http://".$_SERVER['HTTP_HOST'].$file;
	 	$href="http://docs.google.com/viewer?url=".urlencode($file);
	 	header("location:".$href);
	 	break;
}
?>