<?php
/**
 * wch_qiandao.php UTF8
 * User: weicaihong.com
 * Date: 15/1/5 17:35
 * Copyright: http://www.weicaihong.com
 */

// 表前缀 $prefix
$tb_users = $prefix.'users';

$filed = ' pay_points,user_money ';

$user_name = $post_data['shop_username'];
$pay_points = $post_data['user_points'];

$query_sql = "UPDATE `$tb_users` SET `pay_points` = `pay_points`+$pay_points WHERE user_name = '$user_name';";

// 查询sql
$data = array();
$sth = $pdo_db->prepare($query_sql);
$data = $sth->execute();
if($data == TRUE)
{
	$data = array();
	$data['errmsg'] = '';
	$query_sql = "SELECT $filed FROM `$tb_users` WHERE `user_name` = '$user_name' LIMIT 1";
	$sth = $pdo_db->prepare($query_sql);
	$sth->execute();
	$data = $sth->fetch(PDO::FETCH_ASSOC);
	$data['plus_points'] = $pay_points;
}


// 输出json
require_once('wch_json.php');