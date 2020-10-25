<?php mc_template_part('header'); ?>
<div id="carousel-example-generic" class="carousel slide home-slider" data-ride="carousel">
	<?php if(mc_option('homehdimg2')) : ?>
	<ol class="carousel-indicators">
		<li data-target="#carousel-example-generic" data-slide-to="0" class="active">
		</li>
		<li data-target="#carousel-example-generic" data-slide-to="1">
		</li>
		<?php if(mc_option('homehdimg3')) : ?>
		<li data-target="#carousel-example-generic" data-slide-to="2">
		</li>
		<?php endif; ?>
	</ol>
	<?php endif; ?>
	<!-- Wrapper for slides -->
	<div class="carousel-inner" role="listbox">
		<div class="item active">
			<div class="text-center pr" style="background-image: url(<?php echo mc_option('homehdimg1'); ?>);">
				<div class="pr">
				<h1 class="mb-20"><?php echo mc_option('homehdtitle1'); ?></h1>
				<p><?php echo mc_option('homehdtext1'); ?></p>
				<a class="btn btn-warning" href="<?php echo mc_option('homehdlnk1'); ?>"><i class="glyphicon glyphicon-ok-sign"></i> <?php echo mc_option('homehdbtn1'); ?></a>
				</div>
			</div>
		</div>
		<?php if(mc_option('homehdimg2')) : ?>
		<div class="item">
			<div class="text-center pr" style="background-image: url(<?php echo mc_option('homehdimg2'); ?>);">
				<div class="pr">
				<h1 class="mb-20"><?php echo mc_option('homehdtitle2'); ?></h1>
				<p><?php echo mc_option('homehdtext2'); ?></p>
				<a class="btn btn-warning" href="<?php echo mc_option('homehdlnk2'); ?>"><i class="glyphicon glyphicon-ok-sign"></i> <?php echo mc_option('homehdbtn2'); ?></a>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php if(mc_option('homehdimg3')) : ?>
		<div class="item">
			<div class="text-center pr" style="background-image: url(<?php echo mc_option('homehdimg3'); ?>);">
				<div class="pr">
				<h1 class="mb-20"><?php echo mc_option('homehdtitle3'); ?></h1>
				<p><?php echo mc_option('homehdtext3'); ?></p>
				<a class="btn btn-warning" href="<?php echo mc_option('homehdlnk3'); ?>"><i class="glyphicon glyphicon-ok-sign"></i> <?php echo mc_option('homehdbtn3'); ?></a>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>

<!-- 幻灯结束 -->
<div class="container-fluid home-pro">
	<h2 class="title">
		<i class="glyphicon glyphicon-certificate"></i> 推荐商品
		<a class="pull-right" href="<?php echo U('pro/index/index'); ?>">
			查看全部 <i class="glyphicon glyphicon-menu-right"></i>
		</a>
	</h2>
	<div class="row">
		<?php 
			$home_pro = M('page')->where("type='pro'")->order('date desc')->limit(0,8)->select();
			foreach($home_pro as $val) :
		?>
		<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col">
			<a class="pr img-div" href="<?php echo mc_get_url($val['id']); ?>">
				<?php $fmimg_args = mc_get_meta($val['id'],'fmimg',false); $fmimg_args = array_reverse($fmimg_args); ?>
				<img src="<?php echo $fmimg_args[0]; ?>">
				<div class="pa txt">
					<span class="wto"><?php echo $val['title']; ?></span>
					<?php echo mc_price_now($val['id']); ?> <small>元</small>
				</div>
				<div class="pa bg"></div>
			</a>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6 col-lg-8 col home-post">
			<div class="panel panel-default home-panel">
				<div class="panel-heading">
					<i class="glyphicon glyphicon-globe"></i> 社区新帖
					<a class="pull-right" href="<?php echo U('post/group/index'); ?>">
						查看全部 <i class="glyphicon glyphicon-menu-right"></i>
					</a>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6 home-post-tuisong">
							<?php 
								$home_post_tuisong = M('meta')->where("meta_key='tuisong' AND type='basic'")->order('id desc')->limit(0,2)->getField('page_id',true);
								if($home_post_tuisong) :
								foreach($home_post_tuisong as $val) :
							?>
							<a href="<?php echo mc_get_url($val); ?>" class="thumbnail">
								<div class="img-div"><img src="<?php if(mc_get_meta($val,'tuisong')) : echo mc_get_meta($val,'tuisong'); else : echo mc_fmimg($val); endif; ?>" alt="<?php echo mc_get_page_field($val,'title'); ?>"></div>
								<div class="caption">
									<h3 class="wto">
										<?php echo mc_get_page_field($val,'title'); ?>
									</h3>
									<ul class="list-inline mb-0">
										<li><i class="glyphicon glyphicon-star-empty"></i> <?php echo mc_shoucang_count($val); ?></li>
										<li><i class="glyphicon glyphicon-time"></i> <?php  echo mc_format_date(mc_get_meta($val,'time')); ?></li>
									</ul>
									<span class="bg"></span>
								</div>
							</a>
							<?php endforeach; else : ?>
							<div class="nothing">
								暂无推送内容，请在新闻或社区主题的内容页推送文章至此。
							</div>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<ul class="list-unstyled mb-0 home-post-list">
								<?php $page_post = M('page')->where("type='publish'")->order('date desc')->page(1,5)->select(); foreach($page_post as $val) : ?>
								<li id="mc-page-<?php echo $val['id']; ?>">
									<div class="media">
										<?php $author = mc_get_meta($val['id'],'author',true); ?>
										<a class="media-left" href="<?php echo mc_get_url($author); ?>">
											<div class="img-div img-circle">
												<img class="media-object" src="<?php echo mc_user_avatar($author); ?>" alt="<?php echo mc_user_display_name($author); ?>">
											</div>
										</a>
										<div class="media-body">
											<h4 class="media-heading">
												<a class="wto" href="<?php echo mc_get_url($val['id']); ?>"><?php echo $val['title']; ?></a>
											</h4>
											<p class="post-info wto">
												<i class="glyphicon glyphicon-user"></i><a href="<?php echo mc_get_url($author); ?>"><?php echo mc_user_display_name($author); ?></a>
												<i class="glyphicon glyphicon-home"></i><a href="<?php echo mc_get_url(mc_get_meta($val['id'],'group')); ?>"><?php echo mc_get_page_field(mc_get_meta($val['id'],'group'),'title'); ?></a>
												<i class="glyphicon glyphicon-time"></i><?php echo date('m/d H:i',mc_get_meta($val['id'],'time')); ?>
											</p>
										</div>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-lg-4 col">
			<div class="panel panel-default home-panel">
				<div class="panel-heading">
					<i class="glyphicon glyphicon-th-list"></i> 网站新闻
					<a class="pull-right" href="<?php echo U('article/index/index'); ?>">
						查看全部 <i class="glyphicon glyphicon-menu-right"></i>
					</a>
				</div>
				<div class="panel-body">
					<ul class="list-unstyled mb-0 home-article-list">
						<?php $page_article = M('page')->where("type='article'")->order('date desc')->page(1,3)->select(); foreach($page_article as $val) : ?>
						<li id="mc-page-<?php echo $val['id']; ?>">
							<div class="media">
								<a class="media-left" href="<?php echo mc_get_url($val['id']); ?>">
									<div class="img-div">
										<img class="media-object" src="<?php echo mc_fmimg($val['id']); ?>" alt="<?php echo $val['title']; ?>">
									</div>
								</a>
								<div class="media-body">
									<h4 class="media-heading">
										<a class="wto" href="<?php echo mc_get_url($val['id']); ?>"><?php echo $val['title']; ?></a>
									</h4>
									<p class="excerpt mb-0">
										<?php echo mc_cut_str(strip_tags(mc_magic_out($val['content'])),200); ?>
									</p>
								</div>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php $condition['type']  = array('in',array('nav3','nav4')); $nav2 = M('option')->where($condition)->order('id asc')->select(); if($nav2) : ?>
	<ul class="home-nav list-inline">
		<?php foreach($nav2 as $val) : ?>
		<li>
			<a <?php if($val['type']=='nav4') : ?>target="_blank"<?php endif; ?> href="<?php echo $val['meta_value']; ?>">
				<?php echo $val['meta_key']; ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
<?php mc_template_part('footer'); ?>