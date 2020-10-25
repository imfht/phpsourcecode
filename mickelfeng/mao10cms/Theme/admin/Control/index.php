<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<?php 
			$old_ver = mc_option('site_version');
			$ver = $version['ver'];
			$txt = $version['txt'];
			if($ver && $old_ver!=$ver) :
		?>
		<div class="well mt-20">
			<?php if($ver!=1) : ?>
			<a class="btn btn-default" href="<?php echo U('control/index/index?update=mao10cms'); ?>">升级到Mao10cms V<?php echo $ver; ?></a> 注意：点击升级后可能需要几分钟时间的等待！
			<div class="clearfix"></div>
			<?php endif; ?>
			<?php echo $txt; ?>
		</div>
		<?php endif; ?>
		<div id="app-center-home" class="mt-20">
			<div class="row">
				<div class="col-sm-4 col-md-3 col">
					<div class="well text-center">
						<p>注册用户</p>
						<span><?php echo M('page')->where("type = 'user'")->count('id'); ?></span>
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col">
					<div class="well text-center">
						<p>待审主题</p>
						<span><?php echo M('page')->where("type = 'pending'")->count('id'); ?></span>
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col">
					<div class="well text-center">
						<p>订单总数</p>
						<span><?php echo M('action')->where("action_key IN ('trade_wait_send','trade_wait_cofirm','trade_wait_finished','trade_wait_hdfk')")->count('id'); ?></span>
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col">
					<div class="well text-center">
						<p>商品总数</p>
						<span><?php echo M('page')->where("type = 'pro'")->count('id'); ?></span>
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col">
					<div class="well text-center">
						<p>社区主题</p>
						<span><?php echo M('page')->where("type = 'publish'")->count('id'); ?></span>
					</div>
				</div>
				<div class="col-sm-4 col-md-3 col">
					<div class="well text-center">
						<p>评论总数</p>
						<span><?php echo M('action')->where("action_key = 'comment'")->count('id'); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>