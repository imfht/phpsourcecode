<?php

require_once('pub_inc.php');		//公共包含

$howbuy = 'http://www.howbuy.com/trust/ajaxlist.htm?isAjax=true';

$html_h = file_get_contents($howbuy, false, stream_context_create(array('http'=>array(
	'method' => 'POST', 
	'header' => "Content-type:application/x-www-form-urlencoded\r\n", 
	'content'=>"orderField=tjqs&orderType=desc&page=1&perPage=20"))));
if (preg_match_all('/<tr>([^`]*)/', set_end('/<\/tr>/', $html_h), $m)) {
	foreach ($m[1] as $a) {
		if (preg_match('/<td class="tdl"><a href="([^"]*)">([^<]*)<\/a>/', $a, $m)) {
			$c_url = $m[1];
			$title = $m[2];
			if (preg_match('/<td><a[^>]*>([^<]*)<\/a><\/td>[^<]*<td>([^<]*)<\/td>[^<]*<td>([^<]*)<\/td>[^<]*<td>([^<]*)<\/td>[^<]*<td>([^<]*)<\/td>[^<]*<td>([^<]*)<\/td>[^<]*<td class="tdr">([^<]*)<\/td>/', $a, $m)) {
				$title .= ' '.$m[7].' '.$m[4].'年 '.$m[5].'万 '.$m[2].' '.$m[3].' '.$m[6];
				do_pick_content('http://www.howbuy.com'.$c_url, $title);
			}
		}
	}
} 
return true;

function do_pick_content($c_url, $title) {
	$forum_id = 77;
	if (dt_query_one("SELECT id FROM forum_topic WHERE title = '".get_substr($title, 40)."' AND forum_id = '$forum_id' LIMIT 1")) return true;

	$html_c = file_get_contents($c_url);

	//提取内容
	if (preg_match('/<div class="productinfo">[^`]*<!--productinfo end-->/', $html_c, $m)) {
		$content = preg_replace('/h1/', 'h3', preg_replace('/href="[^"]*"|class="[^"]*"/', '', $m[0])).'<br>';
	} else return false;

	if (!do_topic_add($forum_id, $title, $content, 1)) return false;

	return true;
}
