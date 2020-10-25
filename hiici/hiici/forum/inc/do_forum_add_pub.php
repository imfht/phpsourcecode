<?php

//创版公共代码
$ext = intval($_POST['ext']);
$auto_bg_url = empty($_POST['auto_bg_url']) ? 0 : 1;
$auto_intro = empty($_POST['auto_intro']) ? 0 : 1;

$rs = dt_query("INSERT INTO forum (name, auto_bg_url, background_url, auto_intro, intro, user_id, user_name, city, ext, c_at) 
	VALUES ('$name', '$auto_bg_url', '$background_url', '$auto_intro', '$intro', ".$auth['id'].", '".$auth['name']."', '$forum_city', '$ext', ".time().")");
if (!$rs) die('新建forum数据失败！^_^');
