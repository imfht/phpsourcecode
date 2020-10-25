<?php
/**
 * KindEditor PHP
 *
 * 本PHP程序是演示程序，建议不要直接在实际项目中使用。
 * 如果您确定直接使用本程序，使用之前请仔细确认相关安全设置。
 *
 */

require_once 'JSON.php';
// require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/aliyuncs/oss-sdk-php/autoload.php';
// use OSS\OssClient;
// use OSS\Core\OssException;
$php_path = dirname(__FILE__) . '/';
$php_url = dirname($_SERVER['PHP_SELF']) . '/';
//文件保存目录路径
$save_path = $php_path . '../../../../uploads/';

//文件保存目录URL
$save_url = '/uploads/';
//定义允许上传的文件扩展名
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
);
//最大文件大小
$max_size = 1000000;

$save_path = realpath($save_path) . '/';

//PHP上传失败
if (!empty($_FILES['imgFile']['error'])) {
	switch($_FILES['imgFile']['error']){
		case '1':
			$error = '超过php.ini允许的大小。';
			break;
		case '2':
			$error = '超过表单允许的大小。';
			break;
		case '3':
			$error = '图片只有部分被上传。';
			break;
		case '4':
			$error = '请选择图片。';
			break;
		case '6':
			$error = '找不到临时目录。';
			break;
		case '7':
			$error = '写文件到硬盘出错。';
			break;
		case '8':
			$error = 'File upload stopped by extension。';
			break;
		case '999':
		default:
			$error = '未知错误。';
	}
	alert($error);
}

//有上传文件时
if (empty($_FILES) === false) {
	//原文件名
	$file_name = $_FILES['imgFile']['name'];
	//服务器上临时文件名
	$tmp_name = $_FILES['imgFile']['tmp_name'];
	//文件大小
	$file_size = $_FILES['imgFile']['size'];
	//检查文件名
	if (!$file_name) {
		alert("请选择文件。");
	}
	//检查目录
	if (@is_dir($save_path) === false) {
		alert("上传目录不存在。");
	}
	//检查目录写权限
	if (@is_writable($save_path) === false) {
		alert("上传目录没有写权限。");
	}
	//检查是否已上传
	if (@is_uploaded_file($tmp_name) === false) {
		alert("上传失败。");
	}
	//检查文件大小
	if ($file_size > $max_size) {
		alert("上传文件大小超过限制。");
	}
	 
	$dir_name = $_GET['url'];
	//$y_m_d = date("Y-m-d");
	// if (empty($ext_arr[$dir_name])) {
	// 	alert("目录名不正确。");
	// }
	 
	//获得文件扩展名
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	//检查扩展名
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
	}
	//$url = $_GET['url'];
	//创建文件夹
	if ($dir_name !== '') {
		$save_path .= $dir_name . "/";
		$save_url .= $dir_name . "/";
		if (!file_exists($save_path)) {
			mkdir($save_path);
		}
	}

	//$url = $_GET['url'];
	$y_m_d = date("Y-m-d");
	$save_path .= $y_m_d . "/";
	$save_url .= $y_m_d . "/";
	if (!file_exists($save_path)) {
		mkdir($save_path);
	}
	//新文件名
	$new_file_name =  time() . rand(10000, 99999) . '.' . $file_ext;
	;
	//移动文件
	$file_path = $save_path . $new_file_name;

	if (move_uploaded_file($tmp_name, $file_path) === false) {
		alert("上传文件失败。");
	}
	@chmod($file_path, 0644);
	$file_url = $save_url . $new_file_name;
	//echo $file_url;
	//oss_uploadFile($file_url);
	 
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $file_url));
	exit;
}

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}

/**
 * [oss_uploadFile 上传到OSS]
 * @param  [type] $str [description]
 * @return [type]      [description]
 */
// function oss_uploadFile($str){
//     $str  = $str;
//     $oss_url = substr($str,1);
//     $local_url  = $_SERVER['DOCUMENT_ROOT'].$str;
//     $info = uploadFile('qlqwapp',$oss_url,$local_url);
// }

/**
 * 实例化阿里云OSS
 * @return object 实例化得到的对象
 * @return 此步作为共用对象，可提供给多个模块统一调用
 */
// function new_oss(){
//     //实例化OSS
//     $oss['key_id'] = 'LTAIQtoFNLaHlhws';
//     $oss['key_secret'] = 'XSLzUSZkWUkvyfOSXkfiBtRIZwU1iX';
//     $oss['url'] = 'oss-cn-hangzhou.aliyuncs.com';
//     $oss=new \OSS\OssClient($oss['key_id'],$oss['key_secret'],$oss['url']);
//     return $oss;
// }

/**
 * 上传指定的本地文件内容
 *
 * @param OssClient $ossClient OSSClient实例
 * @param string $bucket 存储空间名称
 * @param string $object 上传的文件名称
 * @param string $Path 本地文件路径
 * @return null
//  */
// function uploadFile($bucket,$object,$Path){
   
//     //try 要执行的代码,如果代码执行过程中某一条语句发生异常,则程序直接跳转到CATCH块中,由$e收集错误信息和显示
//     try{
//         //没忘吧，new_oss()是我们上一步所写的自定义函数
//         $ossClient = new_oss();
//         //uploadFile的上传方法
//         $ossClient->uploadFile($bucket, $object, $Path);
//     } catch(OssException $e) {
//         //如果出错这里返回报错信息
//         printf(__FUNCTION__ . ": FAILED\n");
//         printf($e->getMessage() . "\n");
//         return;
//     }
//     //否则，完成上传操作
//     return true;
// }