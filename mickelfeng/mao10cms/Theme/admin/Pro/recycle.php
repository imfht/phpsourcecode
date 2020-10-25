<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<ol class="breadcrumb mb-20 mt-20" id="baobei-term-breadcrumb">
			<li class="hidden-xs">
				<a href="<?php echo U('control/index/index'); ?>">
					首页
				</a>
			</li>
			<li class="hidden-xs">
				<a href="<?php echo U('control/index/pro_index'); ?>">
					商品
				</a>
			</li>
			<li class="active hidden-xs">
				回收站
			</li>
			<div class="pull-right">
				<?php if(mc_is_admin() || mc_is_bianji()) : ?>
				<a href="<?php echo U('control/index/pro_recycle'); ?>">回收站</a>
				<a href="#" data-toggle="modal" data-target="#parameterModal">商品参数</a>
				<a href="#" data-toggle="modal" data-target="#addtermModal">添加分类</a>
				<?php endif; ?>
			</div>
			<div class="clearfix"></div>
		</ol>
		<div id="pro-list">
			<?php foreach($page as $val) : ?>
			<div class="pro-list">
				<div class="row">
					<div class="col-sm-6 col">
						<h4>
							<a class="wto" href="<?php echo U('pro/index/single?id='.$val['id']); ?>"><?php echo $val['title']; ?></a>
						</h4>
					</div>
					<div class="col-sm-3 col">
						<span><?php echo mc_price_now($val['id']); ?></span> <small>元</small>
					</div>
					<div class="col-sm-3 col text-right">
						<a href="<?php echo U('pro/index/single?id='.$val['id']); ?>">
							预览
						</a>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php echo mc_pagenavi($count,$page_now,30); ?>
	</div>
	<?php if(mc_is_admin() || mc_is_bianji()) : ?>
	<!-- Modal -->
	<div class="modal fade" id="addtermModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<form role="form" method="post" action="<?php echo U('home/perform/publish_term'); ?>">
				<div class="modal-body">
					<div class="form-group">
						<label>
							分类名称
						</label>
						<input name="title" type="text" class="form-control" placeholder="">
					</div>
					<?php if($terms_pro) : ?>
					<div class="form-group">
						<label>
							父级分类
						</label>
						<select name="parent" class="form-control">
							<option>
								无父级分类...
							</option>
							<?php foreach($terms_pro as $val) : ?>
							<option value="<?php echo $val['id']; ?>">
								<?php echo $val['title']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php endif; ?>
					<input type="hidden" name="type" value="pro">
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-warning btn-block">
						<i class="glyphicon glyphicon-ok"></i> 保存
					</button>
				</div>
				</form>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="parameterModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<?php $parameter = M('option')->where("meta_key='parameter' AND type='pro'")->select(); if($parameter) : ?>
				<form role="form" method="post" action="<?php echo U('home/perform/pro_parameter_edit'); ?>">
				<div class="modal-body">
					<div class="form-group">
						<label>
							参数列表
						</label>
						<?php foreach($parameter as $par) : ?>
						<div class="input-group">
							<input type="text" class="form-control" value="<?php echo $par['meta_value']; ?>" name="parameter[<?php echo $par['id']; ?>]">
							<span class="input-group-addon" data-dismiss="modal" data-toggle="modal" data-target="#delparameterModal" data-par-id="<?php echo $par['id']; ?>">
								<i class="icon-remove"></i>
							</span>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-warning btn-block">
						<i class="glyphicon glyphicon-ok"></i> 保存
					</button>
				</div>
				</form>
				<?php endif; ?>
				<form role="form" method="post" action="<?php echo U('home/perform/pro_parameter'); ?>">
				<div class="modal-body">
					<div class="form-group">
						<label>
							参数名称
						</label>
						<input name="parameter" type="text" class="form-control" placeholder="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-warning btn-block">
						<i class="glyphicon glyphicon-ok"></i> 保存
					</button>
				</div>
				</form>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<div class="modal fade" id="delparameterModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<div class="modal-body">
					删除操作无法撤销，请务必谨慎！
				</div>
				<div class="modal-footer">
					<form method="post" action="<?php echo U('home/perform/pro_parameter_del'); ?>">
						<button type="submit" class="btn btn-warning btn-block">
							<i class="glyphicon glyphicon-ok"></i> 确认删除
						</button>
						<input type="hidden" name="id" value="">
					</form>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<script>
		$('#parameterModal').on('show.bs.modal', function (e) {
			$('#parameterModal .input-group-addon').click(function(){
				var id = $(this).attr('data-par-id');
				$('#delparameterModal input').val(id);
			});
		})
	</script>
	<div class="modal fade" id="pagedeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title">
						
					</h4>
				</div>
				<div class="modal-body text-center">
					确认要删除这篇文章吗？
				</div>
				<div class="modal-footer" style="text-align:center;">
					<form method="post" action="<?php echo U('home/perform/delete'); ?>">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> 取消
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="glyphicon glyphicon-ok"></i> 确定
					</button>
					<input type="hidden" id="pagedeleteid" name="id" value="">
					</form>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<script>
		$('#pagedeleteModal').on('show.bs.modal', function (e) {
			$('.btn-pagedel').hover(function(){
				var id = $(this).attr('data-par-id');
				$('#pagedeleteModal input').val(id);
			});
		})
	</script>
	<?php endif; ?>
<?php mc_template_part('footer'); ?>