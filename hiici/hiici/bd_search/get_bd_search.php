<?php 

if (empty($_GET)) die;

$time = date("Y-m-d",time());
$html_h = file_get_contents('http://www.baidu.com/s?wd='.urlencode(filter_var($_GET['wd'], FILTER_SANITIZE_STRING)).'&pn='.@intval($_GET['pn']), false, 
	stream_context_create(array('http'=>array('header'=>"X-FORWARDED-FOR:220.169.".mt_rand(1, 255).".".mt_rand(1, 255)."\r\n"))));

if (preg_match_all('/<h3 class="t[^`]*`/', set_end('/class="(c-tools|c)"/', $html_h), $m)) {
	foreach ($m[0] as $html_c) {
		if (preg_match('/"http:\/\/[^\.]*\.[^\.]*\.com[^"]*"[^>]*>([^<]*<\/?e)*[^<]*<\/a>/', $html_c, $m_1)) {
			$hf = preg_replace('/"http[^\}]*\}[^=]*=/', '', $m_1[0]);
		}
		if (preg_match('/<div class="(c-abstract|c-span(18|24) c-span-last|c-clearfix|op_url_size c-row)">(([^<]*<\/?[^d])*[^<]*)(<\/div>|`)/', $html_c, $m_1)) {
			$c_abs = $m_1[3];
		} 
		if (preg_match('/<span class="(g|c-showurl)">(([^<]*<\/?[^s])*[^<]*)<\/span>/', $html_c, $m_1)) {
			$url = $m_1[2];
		} 
?>
		<div class="forum-topic">
			<div class="subject">
				<b class="title">
				<?php echo '<a target="_blank" href='.$hf ?>
				</b>
				<p class="url"><?php echo $url ?></p>
				<p class="info">
				<span><?php echo empty($c_abs) ? null : $c_abs ?></span>
				</p>
			</div>
		</div>
<?php
	}
}
