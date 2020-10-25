<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="row">
			<div class="col-lg-12" id="user-userlist">
				<ul class="media-list">
				<?php foreach($page as $val) : ?>
				<li class="media">
					<a class="pull-left img-div" href="<?php echo U('user/index/index?id='.$val['id']); ?>">
						<img class="media-object" src="<?php echo mc_user_avatar($val['id']); ?>" alt="<?php echo mc_user_display_name($val['id']); ?>">
					</a>
					<div class="media-body">
						<h4 class="media-heading mb-10">
							<a href="<?php echo U('user/index/index?id='.$val['id']); ?>"><?php echo mc_user_display_name($val['id']); ?></a>
							<div class="pull-right">
								<button class="btn btn-default btn-xs user-delete" user-data="<?php echo $val['id']; ?>" data-toggle="modal" data-target="#myModal">
									<i class="glyphicon glyphicon-trash"></i> 删除
								</button>
								<button class="btn btn-default btn-xs user-ip-false" user-data="<?php echo $val['id']; ?>" data-toggle="modal" data-target="#myModal2">
									<i class="glyphicon glyphicon-trash"></i> 屏蔽IP并删除
								</button>
							</div>
						</h4>
						<div class="row">
							<div class="col-xs-6 col-sm-4 col-md-3">
								<?php $user_level = mc_get_meta($val['id'],'user_level',true,'user'); ?>
								<form method="post" action="<?php echo mc_page_url(); ?>">
									<select class="form-control input-sm" name="user_level">
										<option value="1" <?php if($user_level==1) echo 'selected'; ?>>
											普通会员
										</option>
										<option value="6" <?php if($user_level==6) echo 'selected'; ?>>
											网站编辑
										</option>
										<option value="10" <?php if($user_level==10) echo 'selected'; ?>>
											管理员
										</option>
									</select>
									<input type="hidden" name="user_id" value="<?php echo $val['id']; ?>">
								</form>
							</div>
						</div>
					</div>
				</li>
				<?php endforeach; ?>
				</ul>
				<?php echo mc_pagenavi($count,$page_now); ?>
			</div>
		</div>
	</div>
	<?php if(mc_is_admin()) : ?>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						
					</h4>
				</div>
				<div class="modal-body">
					确认要删除这个用户吗？注意：该用户的全部主题也会被一并删除！
				</div>
				<div class="modal-footer">
					<form method="post" action="<?php echo U('home/perform/delete'); ?>">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> 取消
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="glyphicon glyphicon-ok"></i> 确定
					</button>
					<input id="user-delete" type="hidden" name="id" value="">
					</form>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<script>
		$('.user-delete').click(function(){
			var duser = $(this).attr('user-data');
			$('#user-delete').val(duser);
		});
	</script>
	<!-- Modal -->
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						
					</h4>
				</div>
				<div class="modal-body">
					确认要永久屏蔽这个用户的全部IP并删除该用户吗？
				</div>
				<div class="modal-footer">
					<form method="post" action="<?php echo U('home/perform/ip_false'); ?>">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> 取消
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="glyphicon glyphicon-ok"></i> 确定
					</button>
					<input id="user-ip-false" type="hidden" name="id" value="">
					</form>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
	<script>
		$('.user-ip-false').click(function(){
			var duser = $(this).attr('user-data');
			$('#user-ip-false').val(duser);
		}); 
		$('select').change(function(){  
			//var p1 = $(this).children('option:selected').val();//这就是selected的值
			$(this).parent("form").submit();
		});
	</script>
	<?php endif; ?>
<?php mc_template_part('footer'); ?>