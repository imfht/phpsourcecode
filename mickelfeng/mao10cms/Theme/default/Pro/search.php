<?php mc_template_part('header'); ?>
<div class="container-fluid home-pro mt-40">
	<h4 class="title mt-0 mb-20 search-title">
		搜索 "<?php echo $_GET['keyword']; ?>" 的结果
		<ul class="list-inline mb-0 pull-right">
			<li class="active"><a href="<?php echo mc_option('site_url'); ?>/?keyword=<?php echo $_GET['keyword']; ?>&stype=pro">商品</a></li>
			<li><a href="<?php echo mc_option('site_url'); ?>/?keyword=<?php echo $_GET['keyword']; ?>&stype=publish">社区</a></li>
			<li><a href="<?php echo mc_option('site_url'); ?>/?keyword=<?php echo $_GET['keyword']; ?>&stype=article">文章</a></li>
		</ul>
	</h4>
	<div class="row">
		<?php foreach($page as $val) : ?>
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
	<?php echo mc_pagenavi($count,$page_now); ?>
</div>
<?php mc_template_part('footer'); ?>