<?php 

if (empty($_GET['f_url'])) die;
echo file_get_contents(filter_var($_GET['f_url'], FILTER_SANITIZE_URL), false, 
	stream_context_create(array('http'=>array('header'=>"X-FORWARDED-FOR:202.103.".mt_rand(1, 255).".".mt_rand(1, 255)."\r\n"))));
