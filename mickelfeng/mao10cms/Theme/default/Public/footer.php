<footer class="footer">
	<img src="<?php echo mc_theme_url(); ?>/img/logo-footer.png" class="mb-20">
	<div class="clearfix"></div>
	Powered by <a target="_blank" href="http://www.mao10.com/">Mao10CMS V3.5.2</a>. All rights reserved.
	<a id="backtotop" class="goto" href="#site-top"><i class="glyphicon glyphicon-upload"></i></a>
</footer>
<?php if(mc_user_id()) : ?>
<?php else : ?>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				
			</div>
			<div class="modal-body">
				<form role="form" method="post" action="<?php echo U('user/login/submit'); ?>">
					<div class="form-group">
						<input type="text" name="user_name" class="form-control bb-0 input-lg" placeholder="账号" value="<?php echo cookie('user_name'); ?>">
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
					<input type="hidden" name="comefrom" value="<?php echo mc_page_url(); ?>">
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				
			</div>
			<div class="modal-body">
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
					<?php if(mc_option('loginqq')==2) : ?>
					<div class="form-group">
						<a href="<?php echo mc_site_url(); ?>/connect-qq/oauth/index.php"><img src="<?php echo mc_site_url(); ?>/connect-qq/qq_logo.png"></a>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<p class="help-block">
							已有账号<a href="<?php echo U('user/login/index'); ?>">请此登陆</a>
						</p>
					</div>
					<input type="hidden" name="comefrom" value="<?php echo mc_page_url(); ?>">
				</form>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
</body>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo mc_site_url(); ?>/Theme/default/js/bootstrap.min.js"></script>
<script src="<?php echo mc_theme_url(); ?>/js/cat.js"></script>
<?php echo mc_shoucang_js(); ?>
<?php echo mc_guanzhu_js(); ?>
</html>