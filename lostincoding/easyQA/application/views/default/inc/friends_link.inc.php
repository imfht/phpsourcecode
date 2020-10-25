<?php if (!empty($config['friendslink_lists'])): ?>
<div class="fly-link">
    <span>友链：</span>
    <?php foreach ($config['friendslink_lists'] as $_v): ?>
    	<a href="<?=$_v[1]?>" target="_blank"><?=$_v[0]?></a>
	<?php endforeach;?>
</div>
<?php endif;?>