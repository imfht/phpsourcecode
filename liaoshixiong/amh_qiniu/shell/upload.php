<?php
	require_once 	'vendor/autoload.php';
	require_once	'qiniu.config.php';

	use Qiniu\Auth;
	use Qiniu\Storage\UploadManager;
	use Qiniu\Storage\BucketManager;

	$auth = new Auth($accessKey, $secretKey);

	/******************上传文件*****************/
	$token = $auth->uploadToken($bucket);
	$uploadMgr = New UploadManager();

	$fileName = $argv[1];
	$filePath = $argv[2];

	list($ret, $err) = $uploadMgr->putFile($token, $fileName,$filePath);
	if ($err !== null) {
	    var_dump($err);
	}




?>