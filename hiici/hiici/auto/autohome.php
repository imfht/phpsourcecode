<?php

require_once('pub_inc.php');		//公共包含

$forum_id = 101;

$autohome = 'http://dealer.autohome.com.cn/hengyang/0_0_0_0_';

for ($i = 1; $i <= 5; $i++) {
	$html_h = mb_convert_encoding(file_get_contents($autohome.$i.'.html'), 'utf-8', 'gb2312');
	//4S店信息
	if (preg_match_all('/<div class="dealer-cont-bg">[^`]*/', set_end('/<div class="pos-relative">/', $html_h), $m)) {
		$dealer_l = array();
		foreach ($m[0] as $dealer) {
			if (preg_match('/js-dbrand="([^"]*)" js-did="([^"]*)" js-darea="[^"]*" js-haspro="1">([^<]*)<\/a>/', $dealer, $m)) {
				$d_dbrand = $m[1];
				$d_did = $m[2];
				$d_name = $m[3];
			}
			if (preg_match('/dealer-api-phone">([^<]*)<\/span>/', $dealer, $m)) {
				$d_tel = $m[1];
			}
			if (preg_match('/<div title="([^"]*)">[^<]*<a/', $dealer, $m)) {
				$d_adr = $m[1];
			}
			$dealer_l[$d_did] = array($d_name, $d_tel, $d_adr, $d_dbrand);
		}
	}
	//车型链接
	if (preg_match_all('/<a target="_blank" href="(\/([^\/]*)\/spec_([^\.]*)\.html[^"]*)"[^>]*>[^<]*<\/a>/', $html_h, $m)) {
		foreach ($m[1] as $k => $a_u) {
			do_pick_content($m[2][$k], $m[3][$k], 'http://dealer.autohome.com.cn'.$a_u, $dealer_l[$m[2][$k]]);
		}
	} 
}

p_p_topic($forum_id, 1);
return true;


function do_pick_content($h_id_a, $h_id_b, $h_u, $dealer) {
	$h_id = $h_id_a.$h_id_b;
	global $forum_id;

	if (empty($dealer)) return false;

	//避免频繁更新
	$topic = get_topic_by_order_l_n($h_id, $forum_id);
	if (check_topic_live($topic)) return true; 

	$html_c = mb_convert_encoding(file_get_contents($h_u), 'utf-8', 'gb2312');
	//die($html_c);

	//提取内容
	if (preg_match('/<em class="font-bold fn-fontsize16-b">([^<]*)<\/em>/', $html_c, $m)) {
		$price = $m[1];
	}
	die($price);

	if (preg_match('/<span class="text-line font-bold">([^万]*)万<\/span>/', $html_c, $m)) {
		$price_org = $m[1];
	}

	$content = '';
	//提取标题
	if (preg_match('/<p class="title font-yh">([^<]*)<\/p>/', $html_c, $m)) {
		$title =  $m[1].' ['.$dealer[3].']';
		$sou_t = $m[1]; 

		//视频
		do {
			$sou_h = mb_convert_encoding(file_get_contents(mb_convert_encoding('http://sou.autohome.com.cn/shipin?q='.$sou_t, 'gb2312', 'utf-8')), 'utf-8', 'gb2312');
			if (preg_match('/<a href="([^"]*)" data="sequence:1" target="_blank">/', $sou_h, $m)) {
				$f_h = mb_convert_encoding(file_get_contents($m[1]), 'utf-8', 'gb2312');
				if (preg_match('/youkuid=([^"]*)"/', $f_h, $m)) {
					$content .=  '<iframe height=498 width=810 src="http://player.youku.com/embed/'.$m[1].'" frameborder=0 allowfullscreen></iframe><hr>';
					break;
				}
			} 
			$sou_t = preg_replace('/ [^ ]*$/', '', $sou_t, 1);
		} while (preg_match('/ /', $sou_t));
	} 

	//图片
	if (preg_match_all('/<img width="120" height="90" src="([^"]*)">/', $html_c, $m)) {
		$icon_url = preg_replace('/\/s_/', '/u_', $m[1][0]);
		foreach ($m[1] as $i) {
			$content .= '<p><img src="'.preg_replace('/\/s_/', '/u_', $i).'"></p>'; 
		}
	} else return false;
	if (preg_match('/<a href="([^"]*)" class="blue"/', $html_c, $m)) {
		$html_img = file_get_contents('http://dealer.autohome.com.cn/'.$h_id_a.'/'.$m[1]);

		if (preg_match_all('/img src="([^"]*)" width="120"/', $html_img, $m)) {
			foreach ($m[1] as $i) {
				$content .= '<p><img src="'.preg_replace('/\/s_/', '/u_', $i).'"></p>'; 
			}
		} 
	}

	if (preg_match('/(<div class="config current" id="tab-10">[^`]*)<div class="config " id="tab-11">/', $html_c, $m)) {
		$content .= '<hr>'.preg_replace('/class="[^"]*"/', '', $m[1]);
		if (preg_match('/(<div class="config " id="tab-11">[^`]*)<div class="config-pic">/', $html_c, $m)) {
			$content .= '<hr>'.preg_replace('/class="[^"]*"/', '', $m[1]);
			$content = preg_replace('/id="tab-1\d"/', 'class="hidden-xs"', $content);
		}
	}

	if (preg_match('/<input type="hidden" id="endDate" value="([^"]*)"/', $html_c, $m)) {
		$start_t_s = strtotime($m[1]);
	} else return false;

	$content .= '<hr>'.$dealer[0].' '.$dealer[2].' '.$dealer[1];
	$content .= '<style> td, th { padding: 0 50px 5px 0 } </style>';

	
	$phone = $dealer[1];

	do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, $price, $price_org, $phone, $start_t_s, null, $h_id, $topic['id']); 

	return true;
}
