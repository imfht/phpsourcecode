<?php mc_template_part('header'); ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<h4 class="title mt-40 mb-20 search-title">
					搜索 "<?php echo $_GET['keyword']; ?>" 的结果
					<ul class="list-inline mb-0 pull-right">
						<li><a href="<?php echo mc_option('site_url'); ?>/?keyword=<?php echo $_GET['keyword']; ?>&stype=pro">商品</a></li>
						<li><a href="<?php echo mc_option('site_url'); ?>/?keyword=<?php echo $_GET['keyword']; ?>&stype=publish">社区</a></li>
						<li class="active"><a href="<?php echo mc_option('site_url'); ?>/?keyword=<?php echo $_GET['keyword']; ?>&stype=article">文章</a></li>
					</ul>
				</h4>
				<div id="article-list">
					<?php foreach($page as $val) : ?>
						<div class="thumbnail">
								<a href="<?php echo mc_get_url($val['id']); ?>" class="img-div"><img src="<?php echo mc_fmimg($val['id']); ?>" alt="<?php echo $val['title']; ?>"></a>
								<div class="caption">
									<h3>
										<a href="<?php echo mc_get_url($val['id']); ?>"><?php echo $val['title']; ?></a>
									</h3>
									<p>
										<?php echo mc_cut_str(strip_tags(mc_magic_out($val['content'])),200); ?>
									</p>
									<ul class="list-inline">
										<li><i class="glyphicon glyphicon-star-empty"></i> <?php echo mc_shoucang_count($val['id']); ?></li>
										<li><i class="glyphicon glyphicon-time"></i> <?php echo date('Y-m-d',$val['date']); ?></li>
									</ul>
								</div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php echo mc_pagenavi($count,$page_now); ?>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>