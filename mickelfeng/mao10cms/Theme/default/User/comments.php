<?php mc_template_part('header'); ?>
<?php mc_template_part('head-user'); ?>
<?php mc_template_part('head-user-nav'); ?>
	<div class="container">
		<div class="row">
			<div class="col-lg-12" id="post-list-default">
				<ul class="list-group">
				<?php foreach($page as $val) : ?>
				<li class="list-group-item" id="mc-page-<?php echo $val['id']; ?>">
					<p><i class="glyphicon glyphicon-time"></i> <?php echo date('Y-m-d',$val['date']); ?> 在 <a href="<?php echo mc_get_url($val['page_id']); ?>"><?php echo mc_get_page_field($val['page_id'],'title'); ?></a> 发表评论 ：</p>
					<?php echo $val['action_value']; ?>
				</li>
				<?php endforeach; ?>
				</ul>
				<?php echo mc_pagenavi($count,$page_now); ?>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>