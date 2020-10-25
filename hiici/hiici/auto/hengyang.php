<?php

require_once('pub_inc.php');		//公共包含

$today_date = date('Y-m-d', time()-24*3600);

$hengyangs = array('http://www.hengyang.gov.cn/zfxxgk/szfxxgkml/tzgg/tzgg/', 'http://www.hengyang.gov.cn/zfxxgk/szfxxgkml/tzgg/bmxx/', 'http://www.hengyang.gov.cn/zfxxgk/szfxxgkml/rsxx/klzpxb/');
foreach ($hengyangs as $hengyang) {
	$html_h = file_get_contents($hengyang);

	if (preg_match_all('/<a  style="float:left;" href="[^"]*">[^<]*<\/a>[^<]*<[^>]*>[^<]*<\/[^>]*>/', $html_h, $m)) {
		foreach ($m[0] as $a) {
			if (preg_match('/<a  style="float:left;" href="([^"]*)">[^<]*<\/a>[^<]*<[^>]*>([^<]*)<\/[^>]*>/', $a, $m_a)) {
				if (preg_match('/'.$today_date.'/', $m_a[2])) {
					$c_url = preg_replace('/\.\//', $hengyang, $m_a[1]);
					do_pick_content($c_url);
				}
			} 
		}
	} 
}
return true;

function do_pick_content($c_url) {
	$forum_id = 124;
	$html_c = file_get_contents($c_url);

	//提取标题和内容
	if (preg_match('/<div class="content"[^>]*>[^<]*<h2>([^<]*)<\/h2>[^`]*<!-- 结束内容 -->/', $html_c, $m)) {
		$title = $m[1];
	}

	$content = preg_replace('/<!--[^-]*-->|<style[^>]*>[^<]*<\/style>|style="[^"]*"|<script[^>]*>[^`]*<\/script>|<h2>([^<]*)<\/h2>|class="[^"]*"/', '', $m[0]);
	$content = preg_replace('/\.\//', preg_replace('/\/[^\/]*$/', '/', $c_url), $content);

	if (!do_topic_add($forum_id, $title, $content)) return false;

	return true;
}

