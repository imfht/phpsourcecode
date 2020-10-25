<?php
/**
 * wch_jfye.php UTF8
 * User: weicaihong.com
 * Date: 15/1/5 17:35
 * Copyright: http://www.weicaihong.com
 */


// 表前缀 $prefix
$tb_users = $prefix.'users';

// 获取字段 pay_points user_money
$filed = ' pay_points,user_money ';

$user_name = $post_data['shop_username'];

$query_sql = "SELECT $filed FROM `$tb_users` WHERE `user_name` = '$user_name' LIMIT 1";

// 查询sql
$sth = $pdo_db->prepare($query_sql);
$sth->execute();
$data = array();
$data = $sth->fetch(PDO::FETCH_ASSOC);


// 输出json
require_once('wch_json.php');