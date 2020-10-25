<?php

require_once('pub_inc.php');		//公共包含

$zhaozus = array('fangchan' => 'http://m.58.com/hy/fangchan/pn', 'shangpu' => 'http://m.58.com/hy/shangpu/pn', 'zhaozu' => 'http://m.58.com/hy/zhaozu/pn');
foreach ($zhaozus as $key => $zhaozu) {
	for ($i = 1; $i <= 10; $i++) {
		$html_h = file_get_contents_rand_ip($zhaozu.$i);
		if (preg_match_all('/infoid="([^"]*)"/', $html_h, $m)) {
			foreach ($m[1] as $h_id) {
				do_pick_content_zz($h_id, $key);
			}
		} 
	}
}

$zufang = 'http://hy.58.com/zufang/pn';
for ($i = 1; $i <= 10; $i++) {
	$html_h = file_get_contents_rand_ip($zufang.$i);
	if (preg_match_all('/<tr logr="[^"]*(\d{14})_\d_\d[^"]*">/', $html_h, $m)) {
		foreach ($m[1] as $h_id) {
			do_pick_content_zf($h_id);
		}
	} 
}
return true;

//写字楼招租
function do_pick_content_zz($h_id, $key) {
	$forum_id = 116;
	$zz_k_s = array('fangchan' => '厂房', 'shangpu' => '门面', 'zhaozu' => '写字楼');

	//避免频繁更新
	$topic = get_topic_by_order_l_n($h_id, $forum_id);
	if (check_topic_live($topic)) return true; 

	$html_c = file_get_contents_rand_ip('http://m.58.com/hy/'.$key.'/'.$h_id.'x.shtml');

	//提取标题
	if (preg_match('/<h1 class="tit">([^<]*)<\/h1>/', $html_c, $m)) {
		$title = $m[1].' ['.$zz_k_s[$key].']';
		$content = $title.'<hr>';
	} else return false;

	//提取内容
	if (preg_match('/<div id="describe"><p>([^`]*谢谢！)<\/p>/', $html_c, $m)) {
		$content .= preg_replace('/58同城/', 'HIICI', $m[1]).'<hr>';
	} else return false;

	//电话号码
	if (preg_match('/\D(1\d{10})\D/', $html_c, $m)) {
		$phone = $m[1];
		preg_match('/,linkman:\'([^\']*)\',/', $html_c, $m);
		$content .= $m[1].'：'.$phone.'<hr>';
	} else return false;

	//图片
	if (preg_match_all('/ref="(http:\/\/pic[^"]*\.jpg)"/', $html_c, $m)) {
		$icon_url = preg_replace('/small/', 'big', $m[1][0]);
		foreach ($m[1] as $img) {
			$content .= '<img src="'.preg_replace('/small/', 'big', $img).'"/><br>';
		}
	} else return false;

	//提取价格
	if (preg_match('/<strong class="price">([^<]*)<\/strong>/', $html_c, $m)) {
		$price = $m[1];
	} else $price = 0;
	$price_org = $price;

	$start_t_s = time()+15*24*3600;

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, $start_t_s, null, $h_id, $topic['id']); 

	return true;
}

//租房
function do_pick_content_zf($h_id) {
	$forum_id = 42;

	//避免频繁更新
	$topic = get_topic_by_order_l_n($h_id, $forum_id);
	if (check_topic_live($topic)) return true; 

	$html_c = file_get_contents_rand_ip('http://hy.58.com/zufang/'.$h_id.'x.shtml');

	//提取标题
	if (preg_match('/<meta name="description" content=\'([^\']*)\' \/>/', $html_c, $m)) {
		$title = preg_replace('/价格：/', '', (preg_replace('/  */', ' ', $m[1])));
		$content = $title.'<hr>';
	} else return false;

	//电话号码
	if (preg_match('/\d{3} \d{4} \d{4}/', $html_c, $m)) {
		$phone = $m[0];
		preg_match('/,linkman:\'([^\']*)\',/', $html_c, $m);
		$content .= $m[1].'：'.$phone.'<hr>';
	} else return false;

	//图片
	if (preg_match('/<div class="descriptionImg">[^<]*<img src="([^"]*)"\/>[^<]*(<img src="[^"]*"\/>[^<]*)*<\/div>/', $html_c, $m)) {
		$icon_url = $m[1];
		$content .= $m[0];
	} else return false;

	//提取内容
	if (preg_match('/\D(\d*)元\/月/', $title, $m)) {
		$price = $m[1];
		$price_org = $price;
	} else return false; 

	$start_t_s = time()+15*24*3600;

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, $start_t_s, null, $h_id, $topic['id']); 

	return true;
}
