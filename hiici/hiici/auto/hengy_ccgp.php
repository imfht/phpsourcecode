<?php

require_once('pub_inc.php');		//公共包含

$today_date = date('Y-m-d', time()-24*3600);

$hengy_ccgp = 'http://hengy.ccgp-hunan.gov.cn/more.cfm?sid=100002001';
$html_h = file_get_contents($hengy_ccgp);
if (preg_match_all('/<td title="[^"]*"><a[^>]*>[^<]*<\/a><\/td>[^<]*<td[^>]*>[^<]*<\/td>/', $html_h, $m)) {
	foreach ($m[0] as $li) {
		if (preg_match('/<td[^>]*>([^<]*)<\/td>/', $li, $m_li)) {
			if (preg_match('/'.$today_date.'/', $m_li[1])) {
				if (preg_match('/<td title="[^"]*"><a href="([^"]*)"[^>]*>[^<]*<\/a><\/td>/', $li, $m_url)) {
					$m_url[1] = preg_replace('/article\.cfm/', 'news/article_1.cfm', $m_url[1]);
					do_pick_content('http://hengy.ccgp-hunan.gov.cn/'.$m_url[1]);
				}
			}
		} 
	}
} 
return true;

function do_pick_content($c_url) {
	$html_c = file_get_contents($c_url);

	//确定板块ID和提取标题
	if (preg_match('/<font style="font-size:18px; font-weight:bold; line-height:150%;">([^<]*)<\/font>/', $html_c, $m)) {
		$forum_id = 124;
		$title = $m[1];
	}
	//提取内容
	if (preg_match('/<p>[^`]*/', set_end('/<td align="right" height="25">/', $html_c), $m)) {
		$content = $m[0].'<br><br>';
		$content = preg_replace('/line-height: 16pt;/', 'line-height: 19pt;', preg_replace('/font-size: 10.5pt/', 'font-size: 12pt', $content));
	} 

	if (!do_topic_add($forum_id, $title, $content)) return false;

	return true;
}
