<?php
if($rssId)
{	
?>
<a href="<?php echo sys_href($channelId,'rss',$rssId)?>" target="_blank">RSS订阅</a>
<?php
}
else
{
?>
<a href="<?php echo sys_href($channelId)?>" target="_blank">RSS阅览</a>
<?php
}
?>