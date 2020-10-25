<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 37;

$s_url = 'http://hy.58.com/ershoufang/pn';

for ($i = 1; $i <= 10; $i++) {
	$html_h = file_get_contents_rand_ip($s_url.$i);
	if (preg_match_all('/<a href="[^"]*" target="_blank" class="t" infoid="([^"]*)">/', $html_h, $m)) {
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

	$html_c = file_get_contents_rand_ip('http://hy.58.com/ershoufang/'.$h_id.'x.shtml');

	//提取标题
	if (preg_match('/<meta name="description" content=\'([^\']*)\' \/>/', $html_c, $m)) {
		$title =  preg_replace('/售价：|房贷：|产权：|类型：|装修：|；/', '', $m[1]);
		$content = preg_replace('/；/', '<br>', $m[1]).'<hr>';
	} else return false;

	if (preg_match('/id="t_phone">([^<]*)<\/span>/', $html_c, $m)) {
		$phone = $m[1];
		if (preg_match('/,linkman:\'([^\']*)\',/', $html_c, $m)) $content .= $m[1].'：'.$phone.'<hr>';
	} else return false;

	if (preg_match_all('/<div class="descriptionImg">[^<]*(<img src="([^"]*)" alt="[^"]*"\/>)[^<]*<\/div>/', $html_c, $m)) {
		$icon_url = $m[2][0];
		foreach ($m[1] as $i) {
			@$content .= $i;
		}
	} else return false; 

	//提取内容
	if (preg_match('/<span class="bigpri arial">([^<]*)<\/span>/', $html_c, $m)) {
		$price = $m[1];
		$price_org = $price;
	} else return false; 

	$start_t_s = time()+15*24*3600;

	//提取坐标
	if (preg_match('/{"I":6691,"V":"([^"]*)"},{"I":6692,"V":"([^"]*)"}/', $html_c, $m)) {
		$latlng = $m[1].','.$m[2];
	} else return false; 

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price*10000, $price_org*10000, $phone, $start_t_s, null, $h_id, $topic['id']); 
	do_geo_x($latlng, $topic['id']);

	return true;
}
