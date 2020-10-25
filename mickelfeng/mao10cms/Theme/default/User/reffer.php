<?php mc_template_part('header'); ?>
<?php mc_template_part('head-user'); ?>
	<div class="container">
		<div class="home-main" id="reffer">
			<h4 class="title">
				<i class="icon-group" style="top:2px;"></i> 推广 <small>推广功能暂不可用</small>
				<a class="pull-right" style="width:auto; padding:0 15px;" href="<?php echo U('user/index/coins?id='.mc_user_id()); ?>">查看返利记录</a>
			</h4>
			<div class="panel panel-default">
				<div class="panel-body">
					<?php echo mc_get_meta(mc_user_id(),'ref',true,'user'); ?>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>
									我的推广链接
								</label>
								<input type="text" class="form-control text-center" value="<?php echo mc_site_url().'?ref='.mc_user_id(); ?>">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>
									我的推广账号
								</label>
								<input type="text" class="form-control text-center" value="<?php echo mc_get_meta(mc_user_id(),'user_name',true,'user'); ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<li class="list-group-item">
						 <?php echo mc_get_page_field($val['id'],'title'); ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
<?php mc_template_part('footer'); ?>