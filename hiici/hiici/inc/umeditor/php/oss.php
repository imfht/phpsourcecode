<?php

require_once('oss_php_sdk/demo/sample_base.php');

$endpoint = SampleUtil::get_endpoint();
$bucket = SampleUtil::get_bucket_name();
$oss = SampleUtil::get_oss_client();
$options = array();

if ($face_url) {
	$object = preg_replace('/\//', '-', preg_replace('/img\/center\//', '', $face_url));
	$res = $oss->upload_file_by_file($bucket, $object, $face_url, $options);

	$object = preg_replace('/\//', '-', preg_replace('/img\/center\//', '', $face_min_url));
	$res = $oss->upload_file_by_file($bucket, $object, $face_min_url, $options);

	unlink($face_url); unlink($face_min_url);

} else {
	$object = preg_replace('/\//', '-', preg_replace('/upload\//', '', $info['url']));
	$file_path = $info['url'];

	$res = $oss->upload_file_by_file($bucket, $object, $file_path, $options);

	require_once('../../func.php');
	do_rmdir(preg_replace('/\/[^\/]*\..*/', '', $info['url']));

	//如果定义了OSS_URL
	if ($config['OSS_URL']) $info['url'] = 'http://'.$config['OSS_URL'].'/'.$object;
	else $info['url'] = 'http://'.$bucket.'.'.preg_replace('/-internal/', '', $endpoint).'/'.$object;

}
