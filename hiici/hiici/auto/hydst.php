<?php

require_once('pub_inc.php');		//公共包含

$today_date = date('Y-m-d', time()-24*3600);

$hydst = array('http://www.hydst.com/szxw/', 
	'http://www.hydst.com/msxw/', 
	'http://www.hydst.com/hydst/kfxlxq/', 
	'http://www.hydst.com/dspd/dsbd/', 
	'http://www.hydst.com/dspd/lskb/', 
	'http://www.hydst.com/hydst/hyxwlb/');

foreach ($hydst as $h_url) {
	$html_h = file_get_contents($h_url);
	if (preg_match_all('/(<li>(([^<]*<[^>]*>(([^<]*<[^>]*>(([^<]*<img[^>]*>)*)[^<]*<\/[^>]*>)*)[^<]*<\/[^>]*>)*)[^<]*<\/li>)/', $html_h, $m)) {
		foreach ($m[0] as $li) {
			if (preg_match('/<div class="float l_right_05">([^<]*)<\/div>/', $li, $m_li)) {
				if ($today_date == $m_li[1]) {
					if (preg_match('/<a href="([^"]*)"/', $li, $m_url)) {
						do_pick_content('http://www.hydst.com'.$m_url[1]);
					}
				}
			} 
		}
	} 
}
return true;

function do_pick_content($c_url) {
	$forum_id = 93;
	$html_c = file_get_contents($c_url);

	//确定板块ID和提取标题
	if (preg_match('/<strong>([^<]*)<\/strong>/', $html_c, $m)) {
		$title = $m[1];
	}
	//提取内容
	if (preg_match("/javascript:Player\('2','(\d*)','([^']*)'\)/", $html_c, $m)) {
		$c_n = array(57 => '时政新闻', 58 => '民生新闻', 77 => '新闻联播', 79 => '都市报道', 78 => '乡里乡亲', 81 => '楼市快巴');
	} else return false;
	$content = '<video src="http://video1.hydst.com/'.$c_n[$m[1]].'/'.$m[2].'.mp4" controls autoplay width="640"><video>';

	if (preg_match('/<div class="c_info" id="cView">(([^<]*<\w*>([^<]*(<br \/>)*)*[^<]*<\/\w*>)*)[^<]*<\/div>/', $html_c, $m)) {
		$content = '<p>'.$content.'</p><br>'.$m[1].'<br><br><p>来自：衡阳广电网</p>';
	} 

	if (preg_match('/<div class="v_litpic">[^<]*<img src="([^"]*)"/', $html_c, $m)) {
		$icon_url = 'http://www.hydst.com'.$m[1];
	} 

	if (!do_topic_add($forum_id, $title, $content, null, $icon_url)) return false;

	return true;
}
