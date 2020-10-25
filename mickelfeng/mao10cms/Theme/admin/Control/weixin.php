<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="row">
			<div class="col-lg-12">
				<div id="single">
					<div class="row" id="app-weixin">
						<div class="col-sm-4 col-md-4 col-lg-3">
							<div class="list-group" id="app-weixin-side">
								<a href="<?php echo U('control/weixin/index'); ?>" class="list-group-item active">
									接口设置
								</a>
								<a href="<?php echo U('control/weixin/qunfa'); ?>" class="list-group-item">
									信息群发
								</a>
								<a href="<?php echo U('control/weixin/huifu'); ?>" class="list-group-item">
									自动回复
								</a>
							</div>
						</div>
						<div class="col-sm-8 col-md-8 col-lg-9">
							<form role="form" method="post" action="<?php echo mc_page_url(); ?>">
							    <p>
								    注：目前微信接口为开发测试版，仅支持群发文本消息功能和自动回复功能，更多功能会在后续版本加入。
							    </p>
							    <div class="form-group">
							        <label>
							            AppID
							        </label>
							        <input name="weixin_appid" type="text" class="form-control" value="<?php echo mc_option('weixin_appid'); ?>" placeholder="">
							    </div>
							    <div class="form-group">
							        <label>
							            Appsecret
							        </label>
							        <input name="weixin_appsecret" type="text" class="form-control" value="<?php echo mc_option('weixin_appsecret'); ?>" placeholder="">
							    </div>
							    <div class="form-group">
							        <label>
							            Token
							        </label>
							        <input name="weixin_token" type="text" class="form-control" value="<?php echo mc_option('weixin_token'); ?>" placeholder="">
							        <p class="help-block">
							        	以上3项必须全部填写，否则无法保持！
							        </p>
							    </div>
							    <div>
								    <button type="submit" class="btn btn-warning btn-block">
								        <i class="glyphicon glyphicon-ok"></i> 保存
								    </button>
							    </div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>