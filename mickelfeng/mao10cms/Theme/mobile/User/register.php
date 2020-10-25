<?php mc_template_part('header'); ?>
<div class="container" id="login">
	<div class="text-center login-head">
		<a href="<?php echo mc_site_url(); ?>"><img src="<?php echo mc_theme_url(); ?>/img/logo.png"></a>
		<h1>一个帐号，玩转本站所有服务！</h1>
		<p><?php echo mc_option('site_name'); ?></p>
	</div>
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<form role="form" method="post" action="<?php echo U('user/register/submit'); ?>">
				<div class="form-group">
					<input type="text" name="user_name" class="form-control bb-0 input-lg" placeholder="账号">
					<input type="email" name="user_email" class="form-control bb-0 input-lg" placeholder="邮箱">
					<input type="text" name="user_pass" class="form-control bb-0 input-lg password" placeholder="密码">
					<input type="text" name="user_pass2" class="form-control input-lg password" placeholder="重复密码">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-warning btn-block btn-lg">
						立即注册
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
						已有账号<a href="<?php echo U('user/login/index'); ?>">请此登陆</a>
					</p>
				</div>
			</form>
		</div>
	</div>
</div>
<?php mc_template_part('footer'); ?>