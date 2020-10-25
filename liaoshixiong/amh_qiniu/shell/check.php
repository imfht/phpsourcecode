<?php
	require_once 	'vendor/autoload.php';
	require_once	'qiniu.config.php';

	use Qiniu\Auth;
	use Qiniu\Storage\UploadManager;
	use Qiniu\Storage\BucketManager;

	$auth = new Auth($accessKey, $secretKey);

	/******************上传文件*****************/
	$bucketMgr = New BucketManager($auth);

	list($iterms, $marker, $err) = $bucketMgr->listFiles($bucket);
	if ($err !== null) {
	    var_dump($err);
	}else{
		echo '1';
	}




?>