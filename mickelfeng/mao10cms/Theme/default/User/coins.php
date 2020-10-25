<?php mc_template_part('header'); ?>
<?php mc_template_part('head-user'); ?>
	<div class="container">
		<div class="home-main" id="reffer">
			<h4 class="title">
				<i class="glyphicon glyphicon-usd" style="top:2px;"></i> 积分记录
				<a class="pull-right" style="width:auto; padding:0 15px;" href="<?php echo U('user/reffer/index?id='.mc_user_id()); ?>">查看推广记录</a>
			</h4>
			<div class="panel panel-default">
				<div class="panel-body">
					<?php echo mc_get_meta(mc_user_id(),'ref',true,'user'); ?>
					<div class="row">
						<div class="col-sm-6 col-sm-offset-3">
							<div class="form-group">
								<label>
									我的积分（100积分＝1元人民币）
								</label>
								<div class="input-group">
									<input type="text" class="form-control text-center" value="<?php echo mc_coins(mc_user_id()); ?>" disabled>
									<span class="input-group-addon" data-toggle="modal" data-target="#tixianModal">
										申请提现
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<li class="list-group-item">
						<?php if($val['action_value']>0) : ?>
						于 <?php echo date('Y-m-d H:i',$val['date']); ?> 获取 <?php echo $val['action_value']; ?>积分
						<?php elseif($val['action_value']<0) : ?>
						于 <?php echo date('Y-m-d H:i',$val['date']); ?> 提现 <?php echo -$val['action_value']; ?>积分
						<div class="pull-right"><?php echo M('action')->where("action_key='shoukuan' AND page_id='".mc_user_id()."' AND user_id='".mc_user_id()."' AND date='".$val['date']."'")->getField('action_value'); ?> <span class="text-danger"><?php $zhuangtai = M('action')->where("action_key='zhuangtai' AND page_id='".mc_user_id()."' AND user_id='".mc_user_id()."' AND date='".$val['date']."'")->getField('action_value'); if($zhuangtai==1) : echo '等待处理'; elseif($zhuangtai==2) : echo '体现成功'; elseif($zhuangtai==3 || $zhuangtai==4) : echo '收款信息有误'; else : echo '未知状态，请联系管理员'; endif; ?></span></div>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
<div class="modal fade" id="tixianModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				
			</div>
			<div class="modal-body">
				<form id="tixianform" role="form" method="post" action="<?php echo U('user/index/tixian'); ?>">
					<div class="form-group">
						<textarea name="shoukuan" class="form-control input-lg" rows="3" placeholder="您的收款方式，请务必正确填写"></textarea>
					</div>
					<div class="input-group">
						<input name="tixian" type="text" class="form-control input-lg" placeholder="请输入提现金额">
						<span class="input-group-addon">
							暂不可用
						</span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php mc_template_part('footer'); ?>