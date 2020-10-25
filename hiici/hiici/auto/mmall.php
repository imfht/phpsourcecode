<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 112;

$s_url = 'http://www.mmall.com/zhuangxiu/tu/list.html?page=';

for ($i = 2; $i >= 1; $i--) {
	$html_h = file_get_contents_rand_ip($s_url.$i);
	if (preg_match_all('/d="gid(\d*)"/', $html_h, $m)) {
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
	if ($topic) return true; 

	$html_c = file_get_contents_rand_ip('http://www.mmall.com/zhuangxiu/tu/list-'.$h_id.'.html');

	//提取标题
	if (preg_match('/<span id="current_title" class="current">([^<]*)<\/span>/', $html_c, $m)) {
		$title =  $m[1];
		if (preg_match('/<meta name="Keywords" content="([^"]*)"/', $html_c, $m)) {
			$title .= ' '.$m[1];
		}
	} else return false;
	$content = $title.'<hr>';

	//提取内容
	if (preg_match('/<meta name="Description" content="([^"]*)" \/>/', $html_c, $m)) {
		$content .= preg_replace('/红星美凯龙/', '凯龙国际装饰', $m[1]).'<hr>由凯龙国际装饰提供装修整体解决方案<hr>';
	} else return false; 

	$phone = '137-8642-0875';
	$price = 0;
	$price_org = $price;

	if (preg_match_all('/"(http:[^"]*)_100x100\.jpg"/', $html_c, $m)) {
		$icon_url = $m[1][1].'.jpg';
		$i_count = count($m[1])-1;
		foreach ($m[1] as $k => $i) {
			if (0 != $k && $i_count != $k) $content .= '<img src="'.$i.'.jpg" /><br>';
		}
	} else return false; 

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, null, null, $h_id, $topic['id']); 

	return true;
}
