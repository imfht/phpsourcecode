<?php mc_template_part('header'); ?>
	<div class="container">
		<div class="row">
			<div class="col-lg-12" id="post-list-default">
				<div id="post-list-default">
					<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<li class="list-group-item" id="mc-page-<?php echo $val['id']; ?>">
						<div class="row">
							<div class="col-sm-6 col-md-7 col-lg-8">
								<div class="media">
									<?php $author = mc_get_meta($val['id'],'author',true); ?>
									<div class="media-body">
										<h4 class="media-heading">
											<a href="<?php echo mc_get_url($val['id']); ?>"><span class="wto"><?php echo $val['title']; ?></span><?php if($val['date']>strtotime("now")) : ?><span class="label label-danger">置顶</span><?php endif; ?></a>
										</h4>
										<p class="post-info wto">
											<i class="glyphicon glyphicon-user"></i> <?php 
								$type = mc_get_page_field($val['id'],'type');
								if($type=='pro') :
								$type_name = '<a class="pull-left">商品</a>';
								elseif($type=='group') :
								$type_name = '<a class="pull-left">版块</a>';
								elseif($type=='pending') :
								$type_name = '<a class="pull-left">主题-待审</a>';
								elseif($type=='article') :
								$type_name = '<a class="pull-left">新闻</a>';
								else :
								$type_name = '<a class="pull-left">主题</a>';
								endif;
								echo $type_name;
							?>
											<i class="glyphicon glyphicon-time"></i><?php echo date('Y-m-d H:i:s',mc_get_meta($val['id'],'time')); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="col-sm-6 col-md-5 col-lg-4 text-right hidden-xs">
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
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>