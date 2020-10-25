<?php 

if (empty($_GET['wd'])) die;

$c_ip = mt_rand(1, 255).".".mt_rand(1, 255).".".mt_rand(1, 255).".".mt_rand(1, 255);
$html_h = file_get_contents('https://sp0.baidu.com/5a1Fazu8AA54nxGko9WTAnF6hhy/su?wd='.filter_var($_GET['wd'], FILTER_SANITIZE_STRING), false, 
	stream_context_create(array('http'=>array('header'=>"X-FORWARDED-FOR:$c_ip\r\nCLIENT-IP:$c_ip\r\nX-Real-IP:$c_ip\r\n"))));

if (preg_match('/\([^\)]*\)/', $html_h, $m)) {
	$wd_s = mb_convert_encoding($m[0], 'utf-8', 'gb2312');
	echo $wd_s;
} 
