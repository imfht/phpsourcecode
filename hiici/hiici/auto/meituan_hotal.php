<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 104;

$hotal = 'http://hotel.meituan.com/hengyang#ci='.date('Y-m-d', time()).'&co='.date('Y-m-d', time()+24*3600).'&page=';

for ($i = 1; $i <= 25; $i++) {
	$html_h = file_get_contents_m($hotal.$i);
	if (preg_match_all('/http:\/\/www\.meituan\.com\/deal\/(\d*)\.html/', $html_h, $m)) {
		foreach ($m[1] as $h_id) {
			do_pick_content($h_id);
		}
	} 

}

p_p_topic($forum_id);
return true;


function do_pick_content($h_id) {
	global $forum_id;

	//避免频繁更新
	$topic = get_topic_by_order_l_n($h_id, $forum_id);
	if (check_topic_live($topic)) return true; 

	$html_c = file_get_contents_m('http://www.meituan.com/deal/'.$h_id.'.html');

	//提取内容
	if (preg_match('/仅售([^元]*)元/', $html_c, $m)) {
		$price = $m[1];
		if (preg_match('/(原价|价值)([^元]*)元/', $html_c, $m)) {
			$price_org = $m[2];
		}
	} else return false; 

	if (preg_match('/data-src="(http:\/\/[^\/]*\/460\.280\/[^"]*)"/', $html_c, $m)) {
		$icon_url = $m[1];
	} else return false;

	if (preg_match('/<div class=\'deal-term\'>([^`]*)<div id="anchor-reviews"/', $html_c, $m)) {
		$content = preg_replace('/data-src/', 'src', preg_replace('/src="data:[^"]*"|<p class="standard-bar"[^>]*>[^<]*<\/p>|<table width[^`]*<\/tbody>[^<]*<\/table>|<h2[^>]*>[^<]*<\/h2>/', '', $m[1]));
	}

	$html_y = file_get_contents('http://hy.meituan.com/multiact/default', false, stream_context_create(array('http'=>array(
		'method' => 'POST', 
		'header' => "X-Requested-With:XMLHttpRequest\r\nContent-Type:application/x-www-form-urlencoded;charset=UTF-8\r\nCookie:SID=".md5(time().mt_rand(0,1000)).";iuuid=EF0C6A76025114254851A92B5C01D0B592C2D9F965D6467AF03E0604F17327ED;uuid=".md5(time().mt_rand(0,1000)).".".time().".0.0.0", 
		'content'=>"yui=%7B%22args%22%3A$h_id%2C%22act%22%3A%22deal%2Fpoilist%22%7D"))));
	if ($html_y) {
		$adr_s = json_decode($html_y);
		foreach (json_decode($adr_s->yui, true)['265'] as $a) {
			if (265 == $a['city']) {
				@$adr .= $a['name'].'<br>';
				$adr .= $a['address'].'<br>';
				$adr .= $a['phone'].'<br>';

				$name = $a['name'];
				$adr_t = $a['address'];
				$phone = $a['phone'];

				$latlng = $a['latlng'];
				break;
			}
		}
		$adr .= '<hr>';
	} else return false;
	$content = $adr.$content;

	//提取标题
	if (preg_match('/<span class="deal-component-title-prefix">([^<]*)<\/span>/', $html_c, $m)) {
		$title =  $m[1];
		if (preg_match('/<div class="deal-component-description">([^<]*)<\/div>/', $html_c, $m)) {
			$title .= $name.$m[1];
		}
	} 

	if (preg_match('/至\D?(\d{4}.\d{1,2}.\d{1,2})/', $html_c, $m)) {
		$start_t_s = strtotime(preg_replace('/\./', '-', $m[1]));
	} else return false;

	if (preg_match('/name="calendarid" value="([^"]*)"/', $html_c, $m)) {
		$content .= '<script> $(document).ready(function(){ get_content_file("/auto/meituan.html") }) </script>';
		$out_s_u = 'http%3A%2F%2Fwww.meituan.com%2Fdeal%2Fbuy%2F'.$h_id.'%3Fcalendarid%3D'.$m[1];
	}

	$content = preg_replace('/美团/', 'HIICI', $content);

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, $start_t_s, $out_s_u, $h_id, $topic['id']); 
	do_geo_x(preg_replace('/\[|\]/', '', $latlng), $topic['id']);

	return true;
}

function file_get_contents_m($url) {
	return file_get_contents($url, false, stream_context_create(array('http'=>array(
		'method' => 'GET', 
		'header' => "Cookie:SID=".md5(time().mt_rand(0,1000)).";iuuid=EF0C6A76025114254851A92B5C01D0B592C2D9F965D6467AF03E0604F17327ED;uuid=".md5(time().mt_rand(0,1000)).".".time().".0.0.0"))));
}
