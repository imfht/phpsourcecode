<?php

require_once('pub_inc.php');		//公共包含

$s_url = 'http://ju.taobao.com/';

$html_h = file_get_contents_rand_ip($s_url);

if (preg_match_all('/src="(.*\.jpg)"/', $html_h, $m)) {
	$rs = dt_query("UPDATE forum_city_info SET index_img_url = '".$m[1][0]."' WHERE id = 0734");
	if (!$rs) { echo '更新forum_city_info数据失败！'; return false; } 
}

return true;
