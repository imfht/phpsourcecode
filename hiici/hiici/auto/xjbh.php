<?php

require_once('pub_inc.php');		//公共包含

$xjbh_h = 'http://www.xjbh.net';
$xjbhs = array('http://www.xjbh.net/vir/cat5.html?PrmItemsPerPage=1000', 'http://www.xjbh.net/vir/cat13.html?PrmItemsPerPage=1000');
$forum_id = 121;

foreach ($xjbhs as $xjbh) {
	$html_h = file_get_contents($xjbh);
	if (preg_match_all('/<a title="[^"]*".*href="[^"]*" target="_self">[^<]*<img src="[^"]*"([^:]*)/', preg_replace('/特 价:/', '', preg_replace('/现价:/', '', $html_h)), $m)) {
		foreach ($m[0] as $a) {
			preg_match('/<a title="([^"]*)".*href="[^"]*" target="_self">[^<]*<img src="([^"]*)"/', $a, $m_0);

			if (preg_match('/<del>([^<]*)<\/del>/', $a, $m_1)) {
				$price_org = preg_replace('/￥/', '', $m_1[1]);
			}

			if (preg_match('/<em>([^<]*)<\/em>/', $a, $m_2)) {
				$price = preg_replace('/￥/', '', $m_2[1]);
			}

			$topic_id = dt_query_one("SELECT id FROM forum_topic WHERE title = '".$m_0[1]."'")['id'];
			if ($topic_id) {
				dt_query("UPDATE forum_topic_ext_1 SET price = '$price', price_org = '$price_org' WHERE id = '$topic_id'");
				dt_query("UPDATE forum_topic SET l_r_at = ".time()." WHERE id = '$topic_id'");
			} else {	
				do_topic_add_ext_1($forum_id, $m_0[1], '', preg_replace('/s1\.jpg/', 'm0.jpg', $xjbh_h.$m_0[2]), $price, $price_org, '0734-8819500');
			}
		}
		dt_query("UPDATE forum_topic_ext_1 SET price = price_org WHERE price != price_org AND id IN (SELECT id FROM forum_topic WHERE forum_id = '$forum_id' AND l_r_at < ".(time()-2*3600).")");
	} 
}
return true;























//停用
//function do_pick_content($c_url, $icon_url) {
//	global $forum_id;
//	$html_c = file_get_contents($c_url);
//
//	//提取内容
//	if (preg_match('/<span id="productNameSpan">([^<]*)<\/span>/', $html_c, $m)) {
//		$title = $m[1];
//	}
//
//	if (preg_match('/<del>([^<]*)<\/del>/', $html_c, $m)) {
//		$price_org= preg_replace('/￥/', '', $m[1]);
//	}
//
//	if (preg_match('/特.*<em>([^<]*)<\/em>/', $html_c, $m)) {
//		$price= preg_replace('/￥/', '', $m[1]);
//	}
//
//	if (preg_match('/<div id="detailTab1".*class="content"[^>]*>([^`]*)<div id="detailTab2"/', $html_c, $m)) {
//		$content = preg_replace('/div/', 'p', $m[1]);
//	}
//
//	if (!do_topic_add_ext_1($forum_id, $title, $content, $icon_url, $price, $price_org, '0734-8888888')) return false;
//
//	return true;
//}
