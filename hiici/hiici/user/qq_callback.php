<?php 
require_once("qq_login/pub_inc.php");

qq_callback();
get_openid();

if (@empty($_SESSION["openid"])) die;

$password = sha1('QQ');

$user = dt_query_one("SELECT id FROM user WHERE username = '".$_SESSION["openid"]."' AND password = '$password'");
if (!$user) {
	$rs = dt_query("INSERT INTO user (username, password, c_at) VALUES ('".$_SESSION["openid"]."', '$password', ".time().")");
	if (!$rs) { put_info('登录失败！^_^'); header('Location:?c=user&a=login'); die(); }
	$user = dt_query_one("SELECT id FROM user WHERE id = last_insert_id()");
}

require_once('inc/login_pub_inc.php');
die();
