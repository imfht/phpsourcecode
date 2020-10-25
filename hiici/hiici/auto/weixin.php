<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 84; $s_id = null;

$s_url = 'http://weixin.sogou.com/weixin?query=%E8%A1%A1%E9%98%B3&type=2&tsn=0&page=';
//file_get_contents_m($s_url);
for ($i = 1; $i <= 2; $i++) {
	$html_h = file_get_contents_m($s_url.$i, $s_id);

	$html_h = preg_replace('/<em><!--red_beg-->|<!--red_end--><\/em>/', '', $html_h);
	if (preg_match_all('/src="([^"]*)"><\/a>[^e]*<a target="_blank" href="([^"]*)" id="sogou_vr_[^_]*_title_\d"[^>]*>([^<]*)<\/a>/', $html_h, $m)) {
		foreach ($m[3] as $k => $t) {
			do_pick_content($t, $m[2][$k], $m[1][$k]);
		}
	} 
}

function do_pick_content($t, $a, $i_url) {
	global $forum_id, $s_id;

	$html_c = file_get_contents_m(preg_replace('/&amp;/', '&', 'http://weixin.sogou.com'.$a), $s_id);

	$html_c = preg_replace('/<iframe[^>]*data-src="([^"]*)"[^>]*>/', '<iframe src="$1" frameBorder="0" width="670" height="502.5">', $html_c);

	if (preg_match('/<strong class="profile_nickname">([^<]*)<\/strong>/', $html_c, $m)) {
		$t .= ' ['.$m[1].']';
		$content = $m[1];

		if (preg_match('/<span class="profile_meta_value">([^<]*)<\/span>/', $html_c, $m)) {
			$content .= ' ('.$m[1].')';
		}
	}

	if (preg_match('/<div class="rich_media_content "[^>]*>([^`]*)/', set_end('/<\/div>/', $html_c), $m)) {
		$html_c = preg_replace_callback('/<img[^>]*(data-src|src)="([^"]*)"[^>]*>/', function ($m) {
			return '<iframe src_d="'.$m[2].'" frameBorder="0" scrolling="no" width="100%"></iframe>';
		}, $m[1]);

		$content = $html_c.'<p>'.$content.'</p>'.'<br>';

		$i_url = preg_replace('/http:\/\/[^\/]*\/net/', 'http://img01.store.sogou.com/net', $i_url);
		if (!do_topic_add($forum_id, $t, $content, null, $i_url)) return false;

		return true;
	}
}

function file_get_contents_m($url, $snuid = null) {
	$c_ip = mt_rand(1, 255).".".mt_rand(1, 255).".".mt_rand(1, 255).".".mt_rand(1, 255);

	$html_h = file_get_contents($url, false, stream_context_create(array('http'=>array(
		'header' => "Cookie:SUID=BF56E47C7F40900A558A1A55000C89E2;SUV=00F67B297CE456BF558A4ECB354BC387;SNUID=$snuid\r\nX-FORWARDED-FOR:$c_ip\r\nCLIENT-IP:$c_ip\r\nX-Real-IP:$c_ip\r\n"))));

	if (empty($snuid)) {
		if (preg_match('/SNUID=([^;]*);/', $http_response_header[8], $m)) {
			global $s_id; $s_id = $m[1];
		}
	}
	return $html_h;
}
