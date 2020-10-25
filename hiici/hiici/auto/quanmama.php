<?php

require_once('pub_inc.php');		//公共包含

$quanmamas = array(
	array('url' => 'kfc', 'id' => '2581', 'title' => '肯德基'),
	array('url' => 'mdl', 'id' => '2582', 'title' => '麦当劳'),
	array('url' => 'dq', 'id' => '2583', 'title' => 'DQ'),
	array('url' => 'dks', 'id' => '2595', 'title' => '德克士'),
	array('url' => 'starbucks', 'id' => '2623', 'title' => '星巴克'),
	array('url' => 'doulaofang', 'id' => '2624', 'title' => '豆捞坊'),
	array('url' => 'bsk', 'id' => '15596', 'title' => '必胜客'),
	array('url' => 'yonghedawang', 'id' => '2594', 'title' => '永和大王')
);

foreach ($quanmamas as $q) {
	$html_h = file_get_contents('http://www.quanmama.com/'.$q['url'].'/zhengzhang');

	if (preg_match('/<div class="pfet_img" id="my_area" style="width: 960px">(([^<]*<[^>]*>[^<]*<[^>]*>[^<]*<\/[^>]*>)*)/', $html_h, $m)) {
		$content = preg_replace('/href="[^"]*"/', '', $m[1]);
		if (!empty($content)) dt_query("UPDATE forum_topic SET title = '【".$q['title']."】优惠券-".date('Y.m.d', time())."', content = '$content', l_r_at = ".time()." WHERE id = ".$q['id']);
	}
}

return true;
