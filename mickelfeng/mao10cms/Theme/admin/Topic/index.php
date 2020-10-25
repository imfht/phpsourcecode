<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<ol class="breadcrumb mb-20 mt-20" id="baobei-term-breadcrumb">
			<li>
				<a href="<?php echo mc_site_url(); ?>">
					首页
				</a>
			</li>
			<?php if(MODULE_NAME=='Home') : ?>
			<li>
				文章
			</li>
			<li class="active hidden-xs">
				搜索 - <?php echo $_GET['keyword']; ?>
			</li>
			<?php else : ?>
			<li class="active">
				单页
			</li>
			<?php endif; ?>
			<div class="pull-right">
				<?php if(mc_is_admin() || mc_is_bianji()) : ?>
				<a href="<?php echo U('publish/index/add_topic'); ?>" class="publish">新建单页</a>
				<?php endif; ?>
			</div>
		</ol>
		<div id="article-list">
			<?php foreach($page as $val) : ?>
			<div class="pro-list">
				<div class="row">
					<div class="col-sm-9 col">
						<h4>
							<a class="wto" href="<?php echo U('publish/index/edit?id='.$val['id']); ?>"><?php echo $val['title']; ?></a>
						</h4>
					</div>
					<div class="col-sm-3 col text-right">
						<a target="_blank" href="<?php echo mc_get_url($val['id']); ?>">
							查看
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
	</div>
	<?php if(mc_is_admin() || mc_is_bianji()) : ?>
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