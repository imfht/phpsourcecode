<?php
date_default_timezone_set("Asia/Shanghai");
//   1、图片文件上传处理     （上传逻辑就自己写了。）

print_r($_FILES);

//   2、返回值
$file = date("Ymd_").rand(1000,9999).'.jpg';  // 假如这是上传成功后得到的文件名
$res = array(
		'success' => true,
		'file'    => $file
	);
die(json_encode($res));


?>