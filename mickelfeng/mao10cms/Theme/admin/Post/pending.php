<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<ol class="breadcrumb mb-20 mt-20" id="baobei-term-breadcrumb">
			<li>
				<a href="<?php echo mc_site_url(); ?>">
					首页
				</a>
			</li>
			<li>
				<a href="<?php echo U('post/group/index'); ?>">
					社区
				</a>
			</li>
			<li class="active">
				等待审核
			</li>
			<div class="pull-right">
				<?php if(mc_is_admin() || mc_is_bianji()) : ?>
				<a href="<?php echo U('publish/index/add_group'); ?>">新建版块</a>
				<a href="javascript:;" class="publish"><?php echo M('page')->where("type = 'publish'")->count('id'); ?></a>
				
				<?php endif; ?>
			</div>
		</ol>
		<div class="row">
			<div class="col-sm-12" id="group">
				<?php if($page) : ?>
				<div id="post-list-default">
					<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<li class="list-group-item" id="mc-page-<?php echo $val['id']; ?>">
						<div class="row">
							<div class="col-sm-6 col-md-7 col-lg-8">
								<div class="media">
									<?php $author = mc_get_meta($val['id'],'author',true); ?>
									<a class="media-left" href="<?php echo mc_get_url($author); ?>">
										<div class="img-div">
											<img width="40" class="media-object" src="<?php echo mc_user_avatar($author); ?>" alt="<?php echo mc_user_display_name($author); ?>">
										</div>
									</a>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo mc_get_url($val['id']); ?>"><?php echo $val['title']; ?></a>
										</h4>
										<p class="post-info">
											<i class="glyphicon glyphicon-user"></i><a href="<?php echo mc_get_url($author); ?>"><?php echo mc_user_display_name($author); ?></a>
											<i class="glyphicon glyphicon-home"></i><a href="<?php echo mc_get_url(mc_get_meta($val['id'],'group')); ?>"><?php echo mc_get_page_field(mc_get_meta($val['id'],'group'),'title'); ?></a>
											<i class="glyphicon glyphicon-time"></i><?php echo date('Y-m-d H:i:s',$val['date']); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-5 col-lg-4 text-right">
								<ul class="list-inline">
								<?php if(mc_last_comment_user($val['id'])) : ?>
								<li>最后：<?php echo mc_user_display_name(mc_last_comment_user($val['id'])); ?></li>
								<?php endif; ?>
								<li>点击：<?php echo mc_views_count($val['id']); ?></li>
								</ul>
							</div>
						</div>
					</li>
					<?php endforeach; ?>
					</ul>
					<?php echo mc_pagenavi($count,$page_now); ?>
				</div>
				<?php else : ?>
				<div id="post-list-default">
					<ul class="list-group">
						<li class="list-group-item text-center" style="padding:120px 0;">
							暂无任何话题！
						</li>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>