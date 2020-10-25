<?php

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['n_id'])) die;

$n_id = filter_var($_GET['n_id'], FILTER_SANITIZE_STRING);
$n_id_md5 = md5($n_id);

$flex_n_id = dt_query_one("SELECT id FROM flex_n_id WHERE user_id = '".$auth['id']."' ORDER BY c_at DESC LIMIT 1")['id']; 
if ($flex_n_id) {
	$rs = dt_query("UPDATE flex_n_id SET n_id = '$n_id', n_id_md5 = '$n_id_md5', c_at = ".time()." WHERE id = '$flex_n_id'");
	if (!$rs) die('更新n_id数据失败！^_^');
} 

die();
