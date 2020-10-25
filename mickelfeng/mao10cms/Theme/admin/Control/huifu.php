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
								<a href="<?php echo U('control/weixin/qunfa'); ?>" class="list-group-item">
									信息群发
								</a>
								<a href="<?php echo U('control/weixin/huifu'); ?>" class="list-group-item active">
									自动回复
								</a>
							</div>
						</div>
						<div class="col-sm-8 col-md-8 col-lg-9">
							<form class="mb-20" role="form" method="post" action="<?php echo mc_page_url(); ?>">
							    <div class="form-group">
							        <label>
							            用户发送内容
							        </label>
							        <input name="msg" type="text" class="form-control" value="" placeholder="">
							    </div>
							    <div class="form-group">
							        <label>
							            回复用户内容
							        </label>
							        <input name="return" type="text" class="form-control" value="" placeholder="">
							        <p class="help-block">
							        	例如：在用户发送内容中输入“你好”，在回复用户内容中输入“别跟老娘搭讪”。那么当用户通过微信向订阅/服务号发送“你好”时，系统就会自动告诉他：“别跟老娘搭讪”。给客户一个惊喜，就这么简单～～
							        </p>
							    </div>
							    <div>
								    <button type="submit" class="btn btn-warning btn-block">
								        <i class="glyphicon glyphicon-ok"></i> 保存
								    </button>
							    </div>
							</form>
							<?php $page = M('option')->where('type="wx_huifu"')->order('id desc')->page(1,8)->select(); if($page) : ?>
							<ul class="list-group">
								<?php foreach($page as $val) : ?>
								<li class="list-group-item">
									<div class="row">
										<div class="col-sm-4">
											收到：<?php echo $val['meta_key']; ?>
										</div>
										<div class="col-sm-6">
											回复：<?php echo $val['meta_value']; ?>
										</div>
										<div class="col-sm-2">
											<form method="post" action="<?php echo U('control/weixin/huifu'); ?>">
											<button type="submit" class="btn btn-xs btn-warning btn-block">
												删除
											</button>
											<input type="hidden" name="del" value="<?php echo $val['id']; ?>">
											</form>
										</div>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>