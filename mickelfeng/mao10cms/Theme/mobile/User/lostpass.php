<?php mc_template_part('header'); ?>
<div class="container" id="login">
	<div class="text-center login-head">
		<a href="<?php echo mc_site_url(); ?>"><img src="<?php echo mc_theme_url(); ?>/img/logo.png"></a>
		<h1>一个帐号，玩转本站所有服务！</h1>
		<p><?php echo mc_option('site_name'); ?></p>
	</div>
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<form role="form" method="post" action="<?php echo U('user/lostpass/submit'); ?>">
				<div class="form-group">
					<input type="email" name="user_email" class="form-control input-lg" placeholder="邮箱">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-warning btn-block btn-lg">
						找回密码
					</button>
				</div>
				<div class="form-group">
					<p class="help-block">
						<a href="<?php echo U('user/login/index'); ?>">返回登陆</a>
					</p>
				</div>
			</form>
		</div>
	</div>
</div>
<?php mc_template_part('footer'); ?>