<?php mc_template_part('header'); ?>
<?php mc_template_part('head-user'); ?>
<?php mc_template_part('head-user-nav'); ?>
	<div class="container">
		<div class="row">
			<div class="col-lg-12" id="post-list-default">
				<ul class="nav nav-pills nav-justified mb-20">
					<li role="presentation" <?php if($_GET['type']=='') : ?>class="active"<?php endif; ?>>
						<a href="<?php echo U('user/index/shoucang?id='.mc_user_id()); ?>">
							全部
						</a>
					</li>
					<li role="presentation" <?php if($_GET['type']=='pro') : ?>class="active"<?php endif; ?>>
						<a href="<?php echo U('user/index/shoucang?id='.mc_user_id().'&type=pro'); ?>">
							商品
						</a>
					</li>
					<li role="presentation" <?php if($_GET['type']=='publish') : ?>class="active"<?php endif; ?>>
						<a href="<?php echo U('user/index/shoucang?id='.mc_user_id().'&type=publish'); ?>">
							主题
						</a>
					</li>
					<li role="presentation" <?php if($_GET['type']=='article') : ?>class="active"<?php endif; ?>>
						<a href="<?php echo U('user/index/shoucang?id='.mc_user_id().'&type=article'); ?>">
							文章
						</a>
					</li>
				</ul>
				<ul class="list-group">
				<?php foreach($page as $val) : ?>
				<li class="list-group-item" id="mc-page-<?php echo $val['id']; ?>">
					<div class="row">
						<div class="col-sm-6 col-md-7 col-lg-9">
							<div class="media">
								<?php $author = mc_get_meta($val['id'],'author',true); ?>
								<a class="pull-left" href="<?php echo U('user/index/index?id='.$author); ?>">
									<img width="40" class="media-object" src="<?php echo mc_user_avatar($author); ?>" alt="<?php echo mc_user_display_name($author); ?>">
								</a>
								<div class="media-body">
									<h4 class="media-heading">
										<?php 
											$type = mc_get_page_field($val['id'],'type');
										?>
										<a href="<?php echo mc_get_url($val['id']); ?>"><?php echo $val['title']; ?></a>
									</h4>
									<p class="post-info">
										<i class="glyphicon glyphicon-user"></i><a href="<?php echo U('user/index/index?id='.$author); ?>"><?php echo mc_user_display_name($author); ?></a>
										<i class="glyphicon glyphicon-time"></i><?php echo date('Y-m-d H:i:s',$val['date']); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-5 col-lg-3 text-right">
							<?php 
								if($type=='pro') :
								$type_name = '<span class="btn btn-sm btn-info">商品</span>';
								elseif($type=='group') :
								$type_name = '<span class="btn btn-sm btn-info">群组</span>';
								elseif($type=='article') :
								$type_name = '<span class="btn btn-sm btn-info">文章</span>';
								else :
								$type_name = '<span class="btn btn-sm btn-info">主题</span>';
								endif;
								echo $type_name;
							?>
						</div>
					</div>
				</li>
				<?php endforeach; ?>
				</ul>
				<?php echo mc_pagenavi($count,$page_now); ?>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>