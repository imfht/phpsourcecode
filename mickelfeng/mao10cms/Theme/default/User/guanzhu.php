<?php mc_template_part('header'); ?>
<?php mc_template_part('head-user'); ?>
<?php mc_template_part('head-user-nav'); ?>
	<div class="container">
		<div class="row">
			<div class="col-lg-12" id="user-userlist">
				<ul class="media-list">
				<?php foreach($page as $val) : ?>
				<li class="media">
					<a class="pull-left img-div" href="<?php echo U('user/index/index?id='.$val['id']); ?>">
						<img class="media-object" src="<?php echo mc_user_avatar($val['id']); ?>" alt="<?php echo mc_user_display_name($val['id']); ?>">
					</a>
					<div class="media-body">
						<h4 class="media-heading">
							<a href="<?php echo U('user/index/index?id='.$val['id']); ?>"><?php echo mc_user_display_name($val['id']); ?></a>
						</h4>
						<?php echo mc_cut_str(strip_tags(mc_get_page_field($val['id'],'content')), 80); ?>
					</div>
				</li>
				<?php endforeach; ?>
				</ul>
				<?php echo mc_pagenavi($count,$page_now); ?>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>