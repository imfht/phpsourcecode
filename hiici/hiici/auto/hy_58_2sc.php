<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 129;

$s_url = 'http://hy.58.com/ershouche/pn';

for ($i = 1; $i <= 10; $i++) {
	$html_h = file_get_contents_rand_ip($s_url.$i);
	if (preg_match_all('/<a href="http:\/\/hy\.58\.com\/ershouche\/([^x]*)x\.shtml[^"]*" target="_blank">/', $html_h, $m)) {
		foreach ($m[1] as $h_id) {
				do_pick_content($h_id);
		}
	} 
}
return true;


function do_pick_content($h_id) {
	global $forum_id;

	//避免频繁更新
	$topic = get_topic_by_order_l_n($h_id, $forum_id);
	if (check_topic_live($topic)) return true; 

	$html_c = file_get_contents_rand_ip('http://hy.58.com/ershouche/'.$h_id.'x.shtml');

	//提取标题
	if (preg_match('/<div id="content_sumary_right">[^<]*<h1 class="h1">([^<]*)<\/h1>/', $html_c, $m)) {
		$title = $m[1];
	} else return false;

	if (preg_match('/(<ul class="car_info_param">[^`]*)<div id="liangdian">/', $html_c, $m)) {
		$content = $title.'<hr>'.preg_replace('/class="[^"]*"/', '', $m[1]).'<hr>';
	} else return false; 

	//电话号码
	if (preg_match('/<span class="font20" id="t_phone">[^\d]*(\d{3})<i class="color_888">-<\/i>(\d{4})<i class="color_888">-<\/i>[^\d]*(\d{4})/', $html_c, $m)) {
		$phone = $m[1].'-'.$m[2].'-'.$m[3];
		if (preg_match('/,linkman:\'([^\']*)\',/', $html_c, $m)) $content .= $m[1].'：'.$phone.'<hr>';
	} else return false;

	//图片
	if (preg_match_all('/<img src=\'([^\']*)\' id="pic\d" class="mb_4"\/>/', $html_c, $m)) {
		$icon_url = $m[1][0];
		foreach ($m[0] as $i) {
			$content .= $i;
		}
	} else return false;

	//提取内容
	if (preg_match('/name="daikuanje" value=(\d*\.\d*) \/>/', $html_c, $m)) {
		$price = $m[1];
		$price_org = $price;
	} else return false; 

	$start_t_s = time()+15*24*3600;

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price*10000, $price_org*10000, $phone, $start_t_s, null, $h_id, $topic['id']); 

	return true;
}
