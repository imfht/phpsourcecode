<?php
	require_once 	'vendor/autoload.php';
	require_once	'qiniu.config.php';

	use Qiniu\Auth;
	use Qiniu\Storage\BucketManager;

	$auth = new Auth($accessKey, $secretKey);

	/******************下载文件*****************/
	$fileName = $argv[1];
	
	$baseUrl = 'http://'.$domain.'/'.$fileName;
	$authUrl = $auth->privateDownloadUrl($baseUrl);

	//下载到本地
	$filePath = '/home/backup/'
	$content = file_get_contents($authUrl);
	$fp = fopen($filePath.$fileName, 'w');
	fwrite($fp, $content);
	fclose($fp);


?>