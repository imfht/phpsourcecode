<?php mc_template_part('header'); ?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<ol class="breadcrumb mb-20 mt-40" id="baobei-term-breadcrumb">
					<li>
						<a href="<?php echo mc_site_url(); ?>">
							首页
						</a>
					</li>
					<?php if(MODULE_NAME=='Home') : ?>
					<li>
						新闻
					</li>
					<li class="active hidden-xs">
						搜索 - <?php echo $_GET['keyword']; ?>
					</li>
					<?php else : ?>
					<li class="active">
						新闻
					</li>
					<?php endif; ?>
				</ol>
				<?php $terms_article = M('page')->where('type="term_article"')->order('id desc')->select(); if($terms_article) : ?>
				<ul class="nav nav-pills mb-10 term-list" role="tablist">
					<li class="active">
						<a href="javascirpt:;">全部</a>
					</li>
				<?php foreach($terms_article as $val) : ?>
					<li>
						<a href="<?php echo U('article/index/term?id='.$val['id']); ?>">
							<?php echo $val['title']; ?>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
				<?php endif; ?>
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