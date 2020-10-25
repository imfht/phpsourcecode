<?php mc_template_part('header'); ?>
<div class="container" id="login">
	<div class="text-center login-head">
		<a href="<?php echo mc_site_url(); ?>"><img src="<?php echo mc_theme_url(); ?>/img/logo.png"></a>
		<h1>一个帐号，玩转本站所有服务！</h1>
		<p><?php echo mc_option('site_name'); ?></p>
	</div>
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<form role="form" method="post" action="<?php echo U('user/login/submit'); ?>">
				<div class="form-group">
					<input type="text" name="user_name" class="form-control bb-0 input-lg" placeholder="账号">
					<input type="text" name="user_pass" class="form-control input-lg password" placeholder="密码">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-warning btn-block btn-lg">
						立即登陆
					</button>
				</div>
				<div class="form-group">
					<?php if(mc_option('loginqq')==2) : ?>
					<a href="<?php echo mc_site_url(); ?>/connect-qq/oauth/index.php"><img src="<?php echo mc_site_url(); ?>/connect-qq/qq_logo.png"></a>
					<?php endif; ?>
					<?php if(mc_option('loginweibo')==2) : ?>
					<a href="<?php echo mc_site_url(); ?>/connect-weibo/oauth/index.php"><img src="<?php echo mc_site_url(); ?>/connect-weibo/weibo_logo.png"></a>
					<?php endif; ?>
				</div>
				<div class="form-group">
					<p class="help-block">
						<a href="<?php echo U('user/lostpass/index'); ?>">忘记密码？</a>
					</p>
				</div>
				<div class="form-group">
					<a href="<?php echo U('user/register/index'); ?>" class="btn btn-default btn-block btn-lg">
						注册账号
					</a>
				</div>
			</form>
		</div>
	</div>
</div>
<?php mc_template_part('footer'); ?>