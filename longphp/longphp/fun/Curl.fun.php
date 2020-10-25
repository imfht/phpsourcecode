<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

function curl($url, $t = 'GET', $data = '', $header = array()){
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36');

    if(!empty($header)){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

	if(!empty($data)){
        if(is_array($data)){
		    $data = http_build_query($data);
        }
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $t);
	
	$con = curl_exec($ch);
	curl_close($ch);
	return $con;
}
