<?php mc_template_part('header'); ?>
	<div class="container-admin">
		<form class="mb-20" role="form" method="post" action="<?php echo U('user/index/site_nav'); ?>">
			<div class="form-group">
				<label>头部导航设置</label>
				<div class="row">
					<div class="col-xs-7 col">
						<div class="input-group">
							<span class="input-group-addon">
								<input type="checkbox" name="nav_blank" value="1">
							</span>
							<input type="text" name="nav_text" class="form-control" placeholder="新增导航文本">
						</div>
					</div>
					<div class="col-xs-5 col">
						<input type="text" name="nav_link" class="form-control" placeholder="新增导航链接">
					</div>
				</div>
			</div>
			<div class="form-group">注：勾选即可新窗口打开</div>
			<input name="nav_control" type="hidden" value="ok">
			<button type="submit" class="btn btn-warning btn-block">
				<i class="glyphicon glyphicon-ok-circle"></i> 保存
			</button>
		</form>
		<?php 
			$condition['type']  = array('in',array('nav','nav2'));
			$nav = M('option')->where($condition)->order('id asc')->select(); ?>
		<?php foreach($nav as $val) : ?>
		<div class="input-group mb-10">
			<input type="text" class="form-control" value="<?php echo $val['meta_key']; ?> <?php if($val['type']=='nav2') echo '- 新窗口'; ?>" disabled>
			<span class="input-group-addon" data-dismiss="modal" data-toggle="modal" data-target="#delnavModal" data-nav-id="<?php echo $val['id']; ?>">
				<i class="glyphicon glyphicon-remove-circle"></i>
			</span>
		</div>
		<?php endforeach; ?>
		<form class="mb-20 mt-20" role="form" method="post" action="<?php echo U('user/index/site_nav2'); ?>">
			<div class="form-group">
				<label>底部导航设置</label>
				<div class="row">
					<div class="col-xs-7 col">
						<div class="input-group">
							<span class="input-group-addon">
								<input type="checkbox" name="nav_blank" value="1">
							</span>
							<input type="text" name="nav_text" class="form-control" placeholder="新增导航文本">
						</div>
					</div>
					<div class="col-xs-5 col">
						<input type="text" name="nav_link" class="form-control" placeholder="新增导航链接">
					</div>
				</div>
			</div>
			<div class="form-group">注：勾选即可新窗口打开</div>
			<input name="nav_control" type="hidden" value="ok">
			<button type="submit" class="btn btn-warning btn-block">
				<i class="glyphicon glyphicon-ok-circle"></i> 保存
			</button>
		</form>
		<?php 
			$condition['type']  = array('in',array('nav3','nav4'));
			$nav = M('option')->where($condition)->order('id asc')->select(); ?>
		<?php foreach($nav as $val) : ?>
		<div class="input-group mb-10">
			<input type="text" class="form-control" value="<?php echo $val['meta_key']; ?> <?php if($val['type']=='nav4') echo '- 新窗口'; ?>" disabled>
			<span class="input-group-addon" data-dismiss="modal" data-toggle="modal" data-target="#delnavModal" data-nav-id="<?php echo $val['id']; ?>">
				<i class="glyphicon glyphicon-remove-circle"></i>
			</span>
		</div>
		<?php endforeach; ?>
	</div>
<div class="modal fade" id="delnavModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				
			</div>
			<div class="modal-body">
				删除操作无法撤销，请务必谨慎！
			</div>
			<div class="modal-footer">
				<form method="post" action="<?php echo U('home/perform/nav_del'); ?>">
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
	$('.input-group-addon').click(function(){
		var id = $(this).attr('data-nav-id');
		$('#delnavModal input').val(id);
	})
</script>
<?php mc_template_part('footer'); ?>