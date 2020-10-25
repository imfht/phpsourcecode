<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 117;
$hy_58_huangye = 'http://hy.58.com/huangye/';

$html_h = file_get_contents($hy_58_huangye);
if (preg_match_all('/<a href="[^"]*" class="all0">[^<]*<\/a>/', $html_h, $m)) {
	foreach ($m[0] as $a) {
		if (preg_match('/<a href="([^"]*)" class="all0">([^<]*)<\/a>/', $a, $m)) {
			$l_url = $m[1]; $sv = $m[2];
			for ($i = 1; $i <= 2; $i++) {
				$html_l = file_get_contents_rand_ip('http://hy.58.com'.$l_url.'pn'.$i.'/');
				if (preg_match_all('/<tr  logr[^>]*>[^`]*/', set_end('/<\/tr>/', $html_l), $m)) {
					foreach ($m[0] as $l) {
					if (preg_match('/<b class="f14">([^<]*)<\/b>[^<]*<a href=\'[^\']*\' target="_blank" class="t" >([^<]*)<\/a>/', $l, $m)) {
							if (preg_match('/\D(\d{11})\D/', $l, $m_1)) {
								$title = $m[1].$m_1[1].$m[2].$sv;
								if (preg_match('/\t*(.*\.\.\.)/', $l, $m)) {
									$content = $m[1];
								}
								do_topic_add($forum_id, $title, $content);
							}
						}
					}
				}
			}
		}
	}
}
return true;
