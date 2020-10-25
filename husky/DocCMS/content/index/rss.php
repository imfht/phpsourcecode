<?php
function shl_rss($channelId,$rssId=0,$n=30,$style=0){
	//$channelId为创建的rss频道；$rssId为要生成rss订阅的频道；$n为显示的最新信息的条数；$style为调用样式
	if(!is_int($channelId))return ('parameters $channelId is not integer in doc_rss()!');
	if(!is_int($rssId))return ('parameters $rssId is not integer in doc_rss()!');
	if(!is_int($n))return ('parameters $n is not integer in doc_rss()!');
	if(!is_int($style))return ('parameters $style is not integer in doc_rss()!');
	global $tag;
	require(get_style_file('rss','rss',$style));
}
function doc_rss($channelId=0,$rssId=0,$n=30,$style=0)
{
	shl_rss($channelId,$rssId,$n,$style);
}
?>