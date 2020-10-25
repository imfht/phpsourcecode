<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="home-main" id="reffer">
			<div class="panel panel-default">
				<ul class="list-group">
					<?php foreach($page as $val) : ?>
					<li class="list-group-item">
						<div class="row">
							<div class="col-sm-10">
								<?php echo mc_user_display_name($val['user_id']); ?> 于 <?php echo date('Y-m-d H:i',$val['date']); ?> 提现 <?php echo -$val['action_value']; ?> 积分
								<span class="pull-right"><?php echo M('action')->where("action_key='shoukuan' AND page_id='".$val['page_id']."' AND user_id='".$val['user_id']."' AND date='".$val['date']."'")->getField('action_value'); ?></span>
							</div>
							<div class="col-sm-2">
								<?php $zt = ''; $ztid = ''; $zhuangtai_arg = M('action')->where("action_key='zhuangtai' AND page_id='".$val['page_id']."' AND user_id='".$val['user_id']."' AND date='".$val['date']."'")->select(); foreach($zhuangtai_arg as $zhuangtai) : $zt = $zhuangtai['action_value']; $ztid = $zhuangtai['id']; endforeach; ?>
								<?php if($zt==2) : ?>
								<form>
									<select class="form-control input-sm">
										<option>
											已处理
										</option>
									</select>
								</form>
								<?php elseif($zt==3) : ?>
								<form>
									<select class="form-control input-sm">
										<option>
											无法处理
										</option>
									</select>
								</form>
								<?php else : ?>
								<form method="post" action="<?php echo mc_page_url(); ?>">
									<select class="form-control input-sm zhuangtai" name="zhuangtai">
										<option value="1" <?php if($zt==1) echo 'selected'; ?>>
											未处理
										</option>
										<option value="2" <?php if($zt==2) echo 'selected'; ?>>
											已处理
										</option>
										<option value="3" <?php if($zt==3) echo 'selected'; ?>>
											无法处理
										</option>
										<option value="4" <?php if($zt!=1 && $zt!=2 && $zt!=3) echo 'selected'; ?>>
											非法提现
										</option>
									</select>
									<input type="hidden" name="id" value="<?php echo $ztid; ?>">
								</form>
								<?php endif; ?>
							</div>
						</div>
					</li>
					<?php endforeach; ?>
					<script>
						$('.zhuangtai').change(function(){  
							//var p1 = $(this).children('option:selected').val();//这就是selected的值
							$(this).parent("form").submit();
						});
					</script>
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
							<i class="icon-credit-card"></i>
						</span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#tixianform .input-group-addon').click(function(){
			$('#tixianform').submit();
		});
	});
</script>
<?php mc_template_part('footer'); ?>