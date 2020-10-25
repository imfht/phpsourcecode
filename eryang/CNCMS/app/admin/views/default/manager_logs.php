<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('main'); ?>"><?php echo lang('set_system'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'manager_log'); ?>"><?php echo $title; ?></a></li>
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

<!--系统日志列表开始-->
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well">
			<h2>
				<i class="icon-info-sign"></i> <?php echo lang('manager_log_list'); ?></h2>
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
						<th><?php echo lang('manager_log_username'); ?></th>
						<th><?php echo lang('manager_log_activity'); ?></th>
						<th><?php echo lang('manager_log_url'); ?></th>
						<th><?php echo lang('manager_log_role'); ?></th>
						<th><?php echo lang('manager_log_time'); ?></th>
                        <th><?php echo lang('manager_log_ip'); ?></th>
                        <th><?php echo lang('manager_log_ip_address'); ?></th>
					</tr>
				</thead>
				<tbody>
					    <?php foreach($manager_log_datas as $n => $datas): ?>
					<tr>
						<td><?php echo $datas['username']; ?></td>
						<td><?php echo word_limiter($datas['activity'],38); ?></td>
						<td><?php echo $datas['url'];?></td>
                        <td><?php echo $datas['role'];?></td>
                        <td><?php echo $datas['time'] == null ? '无' : unix_to_human($datas['time'], TRUE, 'eu');?></td>
						<td><?php echo $datas['ip'] == null ? '无' : $datas['ip'];?></td>
                        <td><?php echo $datas['ip_address'] == null ? '无' : $datas['ip_address'];?></td>
					</tr>
					  <?php endforeach; ?>	
			</table>
            <div class="pagination pagination-centered">
                <?php echo $pagination; ?>

            </div>

		</div>
	</div>
</div>
<!--系统日志列表结束-->
<?php
echo $this->load->view ( 'footer' );
?>
</body>
</html>
