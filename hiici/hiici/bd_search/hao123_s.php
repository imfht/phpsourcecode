<?php

if ('celja9kj' != @$_GET['t']) die;

set_time_limit(3600);

$s_url = 'http://www.hao123.com/';
$html_h = file_get_contents_rand_ip($s_url);

$urls = array(); $u_a = '';
if (preg_match_all('/(www\.hao123\.com\/\w*)"/', $html_h, $m)) {
	foreach ($m[1] as $a) {
		$html_c = file_get_contents_rand_ip('http://'.$a.'/wangzhi');
		if (preg_match_all('/(http:\/\/[^\/|^"]*\/)[^"]*"[^>]*>([^<]*)</', $html_c, $m_1)) {
			foreach ($m_1[0] as $ba) {
				if (preg_match('/(http:\/\/[^\/|^"]*\/)[^"]*"[^>]*>([^<]*)</', $ba, $b)) {
					if (!preg_match('/hao123|miibeian|123juzi|baidu|weibo|chuanke|itunes|wandoujia|tongbu/', $b[1])) {
						$urls[$b[2]] = $b[1];
					}
				}
			}
		}
	}
} 
$urls = array_unique($urls);
foreach ($urls as $k => $u) {
	$u_a .= "'".$k."' => '".$u."', ";
}
file_put_contents('bd_search/hao123_urls.php', "<?php return array($u_a);");
return true;
