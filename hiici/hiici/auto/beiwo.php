<?php

require_once('pub_inc.php');		//公共包含

$s_url = 'http://www.beiwo.tv/list/1/';
$html_h = file_get_contents_rand_ip($s_url);
if (preg_match_all('/<a class="play-img" href="\/vod\/([^\/]*)\/" title="([^"]*)" target="_blank">(<img src="[^"]*"[^>]*\/>)<i><\/i><em>评分：(\d\.\d)<\/em>/', $html_h, $m)) {
	foreach ($m[0] as $a) {
		if (preg_match('/<a class="play-img" href="\/vod\/([^\/]*)\/" title="([^"]*)" target="_blank">(<img src="[^"]*"[^>]*\/>)<i><\/i><em>评分：(\d\.\d)<\/em>/', $a, $m)) {
			do_pick_content($m[1], $m[2], $m[3], $m[4]);
		}
	}
} 
return true;

function do_pick_content($h_id, $title, $img, $score) {
	$forum_id = 132;

	//避免频繁更新
	$topic = get_topic_by_order_l_n($h_id, $forum_id);
	if ($topic) return true; 

	$html_c = file_get_contents_rand_ip('http://www.beiwo.tv/vod/'.$h_id);

	//介绍
	if (preg_match('/<div class="endtext">([^<]*)</', $html_c, $m)) {
		$content = $img.'<hr>'.$m[1].'<br><br><br>'; 
	} else return false;

	//类型
	if (preg_match('/<a href=\'\/list\/[^\/]*\/\'>([^<]*)片<\/a>/', $html_c, $m)) {
		$kind = $m[1]; 
	} else return false;

	//提取迅雷链接
	if (preg_match('/\$([^\$]*)\$###/', $html_c, $m)) {
		$out_s_u = ThunderEncode($m[1]);
	} else return false;

	//图片URL
	if (preg_match('/src="([^"]*)"/', $img, $m)) {
		$icon_url = $m[1];
	} 

	//查询预告片
	$html_c = file_get_contents_rand_ip('http://www.soku.com/search_video/q_'.urlencode($title.' 预告片'));

	if (preg_match('/v_show\/id_([^\.]*)\.html/', $html_c, $m)) {
		$content = '<iframe height=498 width=810 src="http://player.youku.com/embed/'.$m[1].'" frameborder=0 allowfullscreen></iframe><hr>'.$content;
	} else return false;

	$content .= '<script> $(document).ready(function(){ get_content_file("/auto/beiwo.html") }) </script>';

	do_topic_add_x($forum_id, $title.' ['.$kind.'] '.$score, $content, null, $out_s_u, $h_id, null, $icon_url);

	return true;
}

//生成迅雷链接
function ThunderEncode($t_url) {
	$thunderPrefix = "AA";
	$thunderPosix = "ZZ";
	$thunderTitle = "thunder://";
	$tem_t_url = $t_url;
	return $thunderTitle.base64_encode($thunderPrefix.$tem_t_url.$thunderPosix);
}
