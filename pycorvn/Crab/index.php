<?php

/**
 * 
 * 文件上传代码示例：
 * 
 *  // 确保$file为绝对路径
	$file = dirname(__FILE__).'/bg.jpg'; // 图片 
	//$file = dirname(__FILE__).'/a.txt'; // 其他文件
	
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, 'http://crab.oschina.mopaasapp.com/'); 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($curl, CURLOPT_POST, true); 
    curl_setopt($curl, CURLOPT_POSTFIELDS, ['file' => '@'.$file]); 
    $response = curl_exec($curl); 
	curl_close($curl);
    $result = json_decode($response);
	if($result && $result->status == 1) { // 上传成功
		print_r($result->data); // 获取文件存储地址url
	} else {
		throw new Exception($response);
	}
	
 * 访问缩略图方法：
 * 如获得的url为：
 * http://crab.oschina.mopaasapp.com/image/origin/2015/12/11/3d3648e8c205f7662ff16eacd7ba4816.jpeg
 * 在url后附加width和height参数，改变它们的值，即可控制生成缩略图的大小：
 * http://crab.oschina.mopaasapp.com/image/origin/2015/12/11/3d3648e8c205f7662ff16eacd7ba4816.jpeg?width=300&height=200
 */

error_reporting(E_ALL);

define('CR_APP_PATH', __DIR__);

require CR_APP_PATH.'/vendor/Crab.class.php';

Crab::instance()->run();