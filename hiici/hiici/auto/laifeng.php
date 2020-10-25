<?php

require_once('pub_inc.php');		//公共包含
$forum_id = 127;

$s_url = 'http://www.laifeng.com/anchor/search?pageNo=';
for ($i = 1; $i <= 2; $i++) {
	$html_h = file_get_contents_rand_ip($s_url.$i);

	if (preg_match_all('/<a href="\/([^"]*)" target="_blank">[^<]*<img data-cardid="[^"]*" src="([^"]*)" alt="([^"]*)"\/>[^<]*<span class="tag-live">[^星]*<p class="desc">[^<]*<s/', $html_h, $m)) {
		//die($m[0][0]);
		foreach ($m[0] as $a) {
			if (preg_match('/<a href="\/([^"]*)" target="_blank">[^<]*<img data-cardid="[^"]*" src="([^"]*)" alt="([^"]*)"\/>[^<]*<span class="tag-live">[^星]*<p class="desc">([^<]*)<s/', $a, $m)) {
				$h_id = $m[1];

				//避免频繁更新
				$topic = get_topic_by_order_l_n($h_id, $forum_id);
				if (check_topic_live($topic)) continue; 

				$title = $m[3].' ['.$m[4].']';
				$icon_url = $m[2];
				$content = '<p class="flash"><object type="application/x-shockwave-flash" id="ddshowPlayer" name="ddshowPlayer" data="http://static.youku.com/ddshow/a3ef5535/flash/LiveShell.swf" width="810" height="610"><param name="flashvars" value="room_id='.$h_id.'&amp;role=0&amp;autoplay=1&amp;ddshowDomain=www.laifeng.com&amp;userid=1674971123&amp;contrast=0&amp;bright=0&amp;saturation=0&amp;playerwidth810&amp;playerheight=610&amp;fullscreen=1&amp;browser=Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36"><param name="allowFullScreen" value="true"><param name="allowScriptAccess" value="always"><param name="wmode" value="opaque"><param name="bgcolor" value="#000000"></object></p><br>';

				$html_c = file_get_contents_rand_ip('http://www.laifeng.com/room/'.$h_id.'/get_playlist/v2?from=1');
				if (preg_match('/"av":"([^"]*)"/', $html_c, $m)) {
					$content .= '<p class="html5"><video width="810" height="610"  controls="controls" webkit-playsinline=""><source src="'.$m[1].'"></video></p>';
				}
				$content .= '<script> $(document).ready(function(){ get_content_file("/auto/laifeng.html") }) </script>';

				$start_t_s = time()+1*3600; // 1小时

				do_topic_add_ext_1_x($forum_id, $title, $content, $icon_url, 0, 0, '0734-'.$h_id, $start_t_s, null, $h_id, $topic['id']); 
				dt_query("UPDATE forum_topic SET orders = 0, l_r_at = l_r_at - ".(24*3600)." WHERE order_l_n = '$h_id' AND forum_id = '$forum_id'"); //关闭下单
			}
		}
	} 
}
return true;
