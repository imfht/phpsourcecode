<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('main'); ?>"><?php echo lang('set_system'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'manager'); ?>"><?php echo $title; ?></a></li>
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

<!--管理员列表开始-->
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well">
			<h2>
				<i class="icon-info-sign"></i> <?php echo lang('manager_list'); ?></h2>
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
						<th><?php echo lang('manager_status'); ?></th>
						<th><?php echo lang('manager_username'); ?></th>
						<th><?php echo lang('manager_nickname'); ?></th>
						<th><?php echo lang('manager_phone'); ?></th>
						<th><?php echo lang('manager_email'); ?></th>
						<th><?php echo lang('manager_role_id'); ?></th>
						<th><?php echo lang('manager_skin'); ?></th>
						<th><?php echo lang('manager_last_log_time'); ?></th>
						<th><?php echo lang('manager_now_log_time'); ?></th>
						<th><?php echo lang('manager_handle'); ?></th>
					</tr>
				</thead>
				<tbody>
                <?php if($datas):?>
						  	   <?php foreach($datas as $n => $manager_datas): ?>
							<tr>
						<td><?php if($manager_datas['status'] == 1): ?><span
							title="<?php echo lang('manager_status_open'); ?>"
							class="icon icon-unlocked"></span><?php else: ?><span
							title="<?php echo lang('manager_status_close'); ?>"
							class="icon icon-locked"></span><?php endif; ?></td>
						<td><?php echo $manager_datas['username']; ?>	</td>
						<td><?php echo $manager_datas['nickname']==null? '无' :$manager_datas['nickname']; ?></td>
						<td><?php echo $manager_datas['phone']==null? '无' :$manager_datas['phone'];?></td>
						<td><?php echo $manager_datas['email'];?></td>
						<td><?php echo $manager_datas['rolename'];?></td>
						<td><?php echo $manager_datas['skin'];?></td>
						<td><?php echo $manager_datas['last_log_time'] == null ? '无' : unix_to_human($manager_datas['last_log_time'], TRUE, 'eu'); ?></td>
						<td><?php echo $manager_datas['now_log_time'] == null ? '无' : unix_to_human($manager_datas['now_log_time'], TRUE, 'eu');?></td>
						<td>
                            <?php if($manager_datas['id']==1):?>
                                <span title="<?php echo lang('manager_unedit'); ?>"
							class="icon icon-gear"></span> <span
							title="<?php echo lang('manager_undelete'); ?>"
							class="icon  icon-cross"></span>
							<?php else:?><a
							href="<?php echo site_url($this -> config -> item('admin_folder').'manager/form/'.$manager_datas['id']); ?>"
							title="<?php echo lang('manager_edit'); ?>"><span
								title="<?php echo lang('manager_edit'); ?>"
								class="icon  icon-wrench"></span></a>
                                <?php if($manager_datas['id'] == $this -> _manager -> id):?>
                                    <span
                                        title="<?php echo lang('manager_undelete'); ?>"
                                        class="icon  icon-cross"></span>
                                <?php else:?>
                                    <a
							href="javascript:void(0);"
							onclick="return show_delete_confirm('<?php echo lang('delete_message');?>','<?php echo lang('delete_message_confirm');?>', '<?php echo site_url($this -> config -> item('admin_folder').'manager/del/'.$manager_datas['id']); ?>');"
							title="<?php echo lang('manager_delete'); ?>"> <span
								title="<?php echo lang('manager_delete'); ?>"
								class="icon  icon icon-close"></span></a>
                                <?php endif; ?>
                            <?php endif; ?>
										</td>
					</tr>
                               <?php endforeach; ?>
                <?php else:?>
                    <tr><td colspan="10"><div class="center"><h4>无任何数据</h4></div></td></tr>
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
<!--管理员列表结束-->
<?php
echo $this->load->view ( 'footer' );
?>
</body>
</html>
