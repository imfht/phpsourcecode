<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('main'); ?>"><?php echo lang('set_system'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'link'); ?>"><?php echo $title; ?></a></li>
	</ul>
</div>
<!--头部结束-->
<?php
if ($this->session->flashdata ( 'message' )) {
	$message = $this->session->flashdata ( 'message' );
}
if ($this->session->flashdata ( 'error' )) {
	$error = $this->session->flashdata ( 'error' );
}
if (function_exists ( 'validation_errors' ) && validation_errors () != '') {
	$error = validation_errors ();
}
?>
		
		<?php if (!empty($message)): ?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert">×</a>
			<?php echo $message; ?>
		</div>
<?php endif; ?>
						<?php if (!empty($error)): ?>
<div class="alert alert-error">
	<a class="close" data-dismiss="alert">×</a>
			<?php echo $error; ?>
		</div>
<?php endif; ?>

<!--友情链接列表开始-->
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well">
			<h2>
				<i class="icon-info-sign"></i> <?php echo lang('link_list'); ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i
					class="icon-chevron-up"></i></a> <a href="#"
					class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table
				class="table table-striped table-bordered bootstrap-datatable datatable">
				<thead>
					<tr>
						<th><?php echo lang('link_status'); ?></th>
						<th><?php echo lang('link_name'); ?></th>
                        <th><?php echo lang('link_url'); ?></th>
                        <th><?php echo lang('link_thumb'); ?></th>
                        <th><?php echo lang('link_rank'); ?></th>
						<th><?php echo lang('link_addtime'); ?></th>
                        <th><?php echo lang('link_updatetime'); ?></th>
                        <th><?php echo lang('link_handle'); ?></th>
					</tr>
				</thead>
				<tbody>
                               <?php if($datas):?>
						  	   <?php foreach($datas as $n => $link_datas): ?>
							<tr>
						<td><?php if($link_datas['status'] == 1): ?><span
							title="<?php echo lang('link_status_open'); ?>"
							class="icon icon-unlocked"></span><?php else: ?><span
							title="<?php echo lang('link_status_close'); ?>"
							class="icon icon-locked"></span><?php endif; ?></td>
						<td><?php echo word_limiter($link_datas['name'],38); ?>	</td>
                        <td><?php echo $link_datas['url']; ?></td>
                        <td><?php echo $link_datas['thumb']; ?></td>
                        <td><?php echo $link_datas['rank']; ?></td>
						<td><?php echo $link_datas['addtime'] == null ? '无' :  unix_to_human($link_datas['addtime'], TRUE, 'eu');?></td>
                        <td><?php echo $link_datas['updatetime'] == null ? '无' :  unix_to_human($link_datas['updatetime'], TRUE, 'eu');?></td>
						<td>
		<a
							href="<?php echo site_url($this -> config -> item('admin_folder').'link/form/'.$link_datas['id']); ?>"
							title="<?php echo lang('link_edit'); ?>"><span
								title="<?php echo lang('link_edit'); ?>"
                                class="icon  icon-wrench"></span></a>
                            <a
							href="javascript:void(0);"
							onclick="return show_delete_confirm('<?php echo lang('delete_message');?>','<?php echo lang('delete_message_confirm');?>', '<?php echo site_url($this -> config -> item('admin_folder').'link/del/'.$link_datas['id']); ?>');"
							title="<?php echo lang('link_delete'); ?>"> <span
								title="<?php echo lang('link_delete'); ?>"
								class="icon  icon icon-close"></span></a>

										</td>
					</tr>
					  <?php endforeach; ?>
                    <?php else:?>
                                   <tr><td colspan="8"><div class="center"><h4>无任何数据</h4></div></td></tr>
                    <?php endif;?>
			</table>
            <?php if($pagination):?>
            <div class="pagination pagination-centered">
                <?php echo $pagination; ?>

            </div>
            <?php endif;?>
		</div>
	</div>
</div>
<!--友情链接列表结束-->
<?php
echo $this->load->view ( 'footer' );
?>
</body>
</html>
