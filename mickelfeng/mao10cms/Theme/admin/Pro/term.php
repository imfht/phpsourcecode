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
			<?php $parent = mc_get_meta($id,'parent',true,'term'); if($parent) : ?>
			<li class="hidden-xs">
				<a href="<?php echo U('control/index/pro_term?id='.$parent); ?>">
					<?php echo mc_get_page_field($parent,'title'); ?>
				</a>
			</li>
			<?php endif; ?>
			<li class="active hidden-xs">
				<?php echo mc_get_page_field($id,'title'); ?>
			</li>
			<div class="pull-right">
				<?php if(mc_is_admin() || mc_is_bianji()) : ?>
				<a class="hidden-xs" href="#" data-toggle="modal" data-target="#addtermModal">添加分类</a>
				<a href="#" data-toggle="modal" data-target="#edittermModal">编辑分类</a>
				<a href="#" data-toggle="modal" data-target="#deltermModal">删除分类</a>
				<?php endif; ?>
			</div>
			<div class="clearfix"></div>
		</ol>
		<?php 
			if($parent=='') :
			$args_id_t = M('meta')->where("meta_key='parent' AND meta_value='$id' AND type='term'")->getField('page_id',true);
			if($args_id_t) :
			$condition_t['id']  = array('in',$args_id_t);
			$condition_t['type']  = 'term_pro';
			$terms_pro_t = M('page')->where($condition_t)->order('date desc')->select(); 
			endif;
			if($terms_pro_t) :
		?>
		<ul class="nav nav-pills mb-10 term-list" role="tablist">
		<?php foreach($terms_pro_t as $val) : ?>
			<li role="presentation">
				<a href="<?php echo U('control/index/pro_term?id='.$val['id']); ?>">
					<?php echo $val['title']; ?>
				</a>
			</li>
		<?php endforeach; ?>
		</ul>
		<?php endif; endif; ?>
		<div id="pro-list">
			<?php foreach($page as $val) : ?>
			<div class="pro-list">
				<div class="row">
					<div class="col-sm-6 col">
						<h4>
							<a class="wto" href="<?php echo U('publish/index/edit?id='.$val['id']); ?>"><?php echo $val['title']; ?></a>
						</h4>
					</div>
					<div class="col-sm-3 col">
						<span><?php echo mc_price_now($val['id']); ?></span> <small>元</small>
					</div>
					<div class="col-sm-3 col text-right">
						<a href="<?php echo U('home/perform/pro_up?id='.$val['id']); ?>">
							上升
						</a>
						<a href="<?php echo U('home/perform/pro_down?id='.$val['id']); ?>">
							下降
						</a>
						<a href="<?php echo U('publish/index/edit?id='.$val['id']); ?>">
							编辑
						</a>
						<a href="<?php echo U('home/perform/zhiding?id='.$val['id']); ?>">
							置顶
						</a>
						<a href="#" class="btn-pagedel" data-toggle="modal" data-target="#pagedeleteModal" data-par-id="<?php echo $val['id']; ?>">
							删除
						</a>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php echo mc_pagenavi($count,$page_now,30); ?>
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
					<?php 
					$args_id = M('meta')->where("meta_key='parent' AND meta_value>'0' AND type='term'")->getField('page_id',true);
					if($args_id) :
					$condition['id']  = array('not in',$args_id);
					endif;
					$condition['type']  = 'term_pro';
					$terms_pro = M('page')->where($condition)->order('id desc')->select(); 
					if($terms_pro) :
					?>
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
	<div class="modal fade" id="edittermModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<form role="form" method="post" action="<?php echo U('home/perform/edit_term'); ?>">
				<div class="modal-body">
					<div class="form-group">
						<label>
							分类名称
						</label>
						<input name="title" type="text" class="form-control" value="<?php echo mc_get_page_field($id,'title'); ?>" placeholder="">
					</div>
					<?php if(!$terms_pro_t) : if($terms_pro) : ?>
					<div class="form-group">
						<label>
							父级分类
						</label>
						<select name="parent" class="form-control">
							<option>
								无父级分类...
							</option>
							<?php foreach($terms_pro as $val) : ?>
							<?php if($id!=$val['id']) : ?>
							<option value="<?php echo $val['id']; ?>" <?php if($parent==$val['id']) echo 'selected'; ?>>
								<?php echo $val['title']; ?>
							</option>
							<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</div>
					<?php endif; else : ?>
					<p>此分类已为父级分类</p>
					<?php endif; ?>
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" name="paixu">
							编辑的同时，将此分类置于排序第一位
						</label>
					</div>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
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
	<div class="modal fade" id="deltermModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<div class="modal-body text-center">
					<p>确认要删除这个分类吗？</p>
					注意：当前分类下的所有商品都会被删除！
				</div>
				<div class="modal-footer" style="text-align:center;">
					<form method="post" action="<?php echo U('home/perform/delete'); ?>">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<i class="glyphicon glyphicon-remove"></i> 取消
					</button>
					<button type="submit" class="btn btn-danger">
						<i class="glyphicon glyphicon-ok"></i> 确定
					</button>
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					</form>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
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