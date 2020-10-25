<?php 

$forum_citys = array(
	'localhost' => '0001',
	'127.0.0.1' => '0734',
	'zb.hiici.com' => '0001',
	'121.40.76.248' => '0734',
	'www.hiici.com' => '0734',
	'hy.hiici.com' => '0734',
	'news.hiici.com' => '0734',
	'main.hiici.com' => '0734',
	'bbs.hiici.com' => '0734',
	'zhao.hiici.com' => '0734',
	'ent.hiici.com' => '0734',
	'cs.hiici.com' => '0731',
);

$forum_city = @$forum_citys[$_SERVER['SERVER_NAME']];
if (empty($forum_city)) {
	header('Location:'.s_url('?c=index&a=forum_city_choice'));
	die();
}
