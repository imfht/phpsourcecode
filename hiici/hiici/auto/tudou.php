<?php

require_once('pub_inc.php');		//公共包含
$forum_id = 132;

$s_urls = array('http://ent.tudou.com/', 'http://movie.tudou.com/', 'http://music.tudou.com/');

foreach ($s_urls as $s_url) {
	$html_h = file_get_contents_rand_ip($s_url);
	if (preg_match_all('/<a href="([^"]*)" title="([^"]*)"[^>]*><\/a>[^<]*<[^>]*><\/[^>]*>[^<]*<img class="[^"]*" alt="([^"]*)"/', $html_h, $m)) {
		foreach ($m[1] as $k => $m_1) {
			if (!preg_match('/view\/([^\/]*)/', $m_1, $a)) {
				if (!preg_match('/listplay\/[^\/]*\/([^\.]*)/', $m_1, $a)) continue;
			} 
			$m_1 = $a[1];

			//重复检查
			if (dt_query_one("SELECT id FROM forum_topic WHERE out_s_u = '".$m_1."' AND forum_id = '$forum_id' LIMIT 1")) continue; 

			$title = $m[2][$k];
			$content = '<p><iframe src="http://www.tudou.com/programs/view/html5embed.action?type=0&amp;code='.$m_1.'" allowtransparency="true" allowfullscreen="true" allowfullscreeninteractive="true" scrolling="no" border="0" frameborder="0" style="width:810px;height:480px;"></iframe></p><br>';

			do_topic_add_x($forum_id, $title, $content, null, $m_1, null, null, $m[3][$k]);
			dt_query("UPDATE forum_topic SET orders = 0 WHERE out_s_u = '".$m_1."' AND forum_id = '$forum_id'"); //关闭下单
		}
	} 
}
return true;
