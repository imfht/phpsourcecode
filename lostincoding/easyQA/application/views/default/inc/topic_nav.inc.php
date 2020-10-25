<div class="topic_nav clearfix">
	<a class="pjax<?=empty($topic) ? ' active' : ''?>" href="/<?=$active != 'all' ? $active : ''?>">全部</a>
	<?php if (!empty($topic) && !in_array($topic, $config['topic_navs'])): ?>
		<a class="pjax active" href="/topic/articles?topic=<?=urlencode($topic)?>&type=<?=$active?>"><?=$topic?></a>
	<?php endif;?>
	<?php foreach ($config['topic_navs'] as $_v): ?>
		<a class="pjax<?=$topic == $_v ? ' active' : ''?>" href="/topic/articles?topic=<?=urlencode($_v)?>&type=<?=$active?>"><?=$_v?></a>
	<?php endforeach;?>
	<a class="pjax" href="/topic">更多&raquo;</a>
</div>
