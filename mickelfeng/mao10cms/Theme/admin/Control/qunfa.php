<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="row">
			<div class="col-lg-12">
				<div id="single">
					<div class="row" id="app-weixin">
						<div class="col-sm-4 col-md-4 col-lg-3">
							<div class="list-group" id="app-weixin-side">
								<a href="<?php echo U('control/weixin/index'); ?>" class="list-group-item">
									接口设置
								</a>
								<a href="<?php echo U('control/weixin/qunfa'); ?>" class="list-group-item active">
									信息群发
								</a>
								<a href="<?php echo U('control/weixin/huifu'); ?>" class="list-group-item">
									自动回复
								</a>
							</div>
						</div>
						<div class="col-sm-8 col-md-8 col-lg-9">
							<form role="form" method="post" action="<?php echo mc_page_url(); ?>">
							    <div class="form-group">
							        <label>
							            群组ID
							        </label>
							        <input name="group" type="number" class="form-control" value="" placeholder="请输入接收信息的群组ID">
							    </div>
							    <div class="form-group">
							        <label>
							           信息内容
							        </label>
							        <textarea rows="5" class="form-control" name="content" placeholder=""></textarea>
							        <p class="help-block">群发功能暂不稳定，仅供测试使用。</p>
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