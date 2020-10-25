<?php

require_once('pub_inc.php');		//公共包含

$h_url = 'http://m.58.com/salecategory/hy/';
$html_h = file_get_contents_rand_ip($h_url);
if (preg_match_all('/<a href="([^"]*)">(全部|办公设备)<\/a>/', $html_h, $m)) {
	foreach ($m[1] as $a) {
		do_get_a($a);
	}
}
return true;

function do_get_a($a) {
	$html_a = file_get_contents_rand_ip('http://m.58.com'.$a);
	if (preg_match('/<a href="([^"]*)"><h1>/', $html_a, $m)) {
		$cate = $m[1];
		if (preg_match_all('/infoid=\'([^\']*)\'/', $html_a, $m_1)) {
			foreach ($m_1[1] as $h_id) {
				do_pick_content($h_id, $cate);
			}
		}
	}
}
function do_pick_content($h_id, $cate) {
	$forum_id = 130;

	//避免频繁更新
	$topic = get_topic_by_order_l_n($h_id, $forum_id);
	if (check_topic_live($topic)) return true; 

	$html_c = file_get_contents_rand_ip('http://m.58.com'.$cate.$h_id.'x.shtml');

	//提取标题
	if (preg_match('/<h1 class="tit">([^<]*)<\/h1>/', $html_c, $m)) {
		$title = $m[1];
		$content = $title.'<hr>';
	} else return false;

	//提取内容
	if (preg_match('/详细情况<\/h5>([^`]*谢谢！)[^<]*<\/p>/', $html_c, $m)) {
	} else if (preg_match('/<div class="detail_param">([^`]*谢谢！)[^<]*<\/p>/', $html_c, $m)) {
	} else return false;
	$content .= preg_replace('/58同城/', 'HIICI', $m[1]).'<hr>';

	//电话号码
	if (preg_match('/\D(1\d{10})\D/', $html_c, $m)) {
		$phone = $m[1];
		preg_match('/,linkman:\'([^\']*)\',/', $html_c, $m);
		$content .= $m[1].'：'.$phone.'<hr>';
	} else return false;

	//图片
	if (preg_match_all('/ref=\'(http:\/\/pic[^"]*\.jpg)\'/', $html_c, $m)) {
		$icon_url = preg_replace('/small/', 'big', $m[1][0]);
		foreach ($m[1] as $img) {
			$content .= '<img src="'.preg_replace('/small/', 'big', $img).'"/><br>';
		}
	} else return false;

	//提取价格
	if (preg_match('/<strong>&yen;([^<]*)<\/strong>/', $html_c, $m)) {
		$price = $m[1];
	} else $price = 0;
	$price_org = $price;

	$start_t_s = time()+15*24*3600;

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, $start_t_s, null, $h_id, $topic['id']); 

	return true;
}
