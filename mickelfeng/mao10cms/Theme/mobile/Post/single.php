<?php mc_template_part('header'); ?>
	<?php foreach($page as $val) : ?>
	<?php $author = mc_author_id($val['id']); $group_id = mc_get_meta($val['id'],'group'); ?>
	<div class="container-fluid post-single">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<ul class="nav nav-tabs mb-10 post-nav">
					<li role="presentation">
						<a href="<?php echo U('post/group/index'); ?>">
							社区首页
						</a>
					</li>
					<?php $groups = M('page')->where('type="group"')->order('date desc')->select(); if($groups) : foreach($groups as $val_g) : ?>
					<li role="presentation" class="<?php if($group_id==$val_g['id']) echo 'active'; ?>">
						<a href="<?php echo U('post/group/single?id='.$val_g['id']); ?>">
							<?php echo $val_g['title']; ?>
						</a>
					</li>
					<?php endforeach; endif; ?>
				</ul>
				<h1 id="single-title" class="mt-0 mb-20"><?php echo $val['title']; ?></h1>
				<div id="single">
					<div id="entry">
						<?php echo mc_magic_out($val['content']); ?>
					</div>
					<hr>
					<div class="media post-author">
						<div class="media-left">
							<a class="img-div img-circle" href="<?php echo mc_get_url($author); ?>">
								<img class="media-object" src="<?php echo mc_user_avatar($author); ?>" alt="<?php echo mc_user_display_name($author); ?>">
							</a>
						</div>
						<div class="media-body">
							<h4 class="media-heading mb-10">
								<a href="<?php echo mc_get_url($author); ?>"><?php echo mc_user_display_name($author); ?></a>
								<span class="label label-default">作者</span>
							</h4>
							<?php echo mc_get_page_field($author,'content'); ?>
						</div>
					</div>
					<hr>
					<?php echo W("Comment/index",array($val['id'])); ?>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
<?php mc_template_part('footer'); ?>