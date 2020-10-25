<?php
ob_start();
//201201/10
$timeDir = date("Ym")."/".date("d");
$uploadDir = dirname(__FILE__).'/upload/'.$timeDir;
$curDomain = "http://".$_SERVER["HTTP_HOST"]."/";
//相对路径 http://www.ncmem.com/upload/2012-1-10/
$relatPath = $curDomain ."WordPasterChrKindEditor4x/php/upload/" . $timeDir . "/";

//自动创建目录。upload/2012-1-10
if(!is_dir($uploadDir))
{
	mkdir($uploadDir,0777,true);
}

//取时间字符串，格式：09-09_时分秒毫秒
$time = date("His") . floor(microtime() * 10000);
//如果PHP页面为UTF-8编码，请使用urldecode解码文件名称
//$fileName = urldecode($_FILES['postedFile']['name']);
//如果PHP页面为GB2312编码，则可直接读取文件名称
$fileName = urldecode($_FILES['img']['name']);
$tmpName = $_FILES['img']['tmp_name'];

//取文件扩展名jpg,gif,bmp,png
$path_parts = pathinfo($fileName);
$ext = $path_parts["extension"];
$ext = strtolower($ext);//jpg,png,gif,bmp

//只允许上传图片类型的文件
if($ext == "jpg"
	|| $ext == "png"
	|| $ext == "gif"
	|| $ext == "bmp")
{
	//年_月_日_时分秒毫秒.jpg
	$saveFileName = $time . '.' . $ext;

	//xxx/2011_05_05_091250000.jpg
	$savePath = $uploadDir . "/" . $saveFileName;

	//另存为新文件名称
	if (!move_uploaded_file($tmpName,$savePath))
	{
		exit('upload error!' . "文件名称：" .$fileName . "保存路径：" . $savePath);
	}
}

//输出图片路径
//$_SERVER['HTTP_HOST']	localhost:81
//$_SERVER['REQUEST_URI'] /FCKEditor2.4.6.1/php/test.php 
$reqPath = str_replace("upload.php","",$_SERVER['REQUEST_URI']);
echo $relatPath .  $saveFileName;
header('Content-type: text/html; charset=utf-8');
header('Content-Length: ' . ob_get_length());
?>