<?php
echo $this -> load -> view('header');
?>
<!-- 标题开始-->
<div>
	<ul class="breadcrumb">
		<li>
			<a href="<?php echo site_url($this -> config -> item('admin_folder') . 'main'); ?>"><?php echo $nav; ?></a>
		</li>
	</ul>
</div><!--/.标题结束-->
<!-- 内容开始-->
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-info-sign"></i> <?php echo lang('system_message');?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  <thead>
							  <tr>
								  <th><?php echo lang('terrace_message');?></th>
							  </tr>
						  </thead>   
						  <tbody>
							<tr>
								<td><?php echo lang('web_domain_name');?></td>
								<td class="center"><?php echo $_SERVER['SERVER_NAME']; ?></td>
						</tr>
						<tr>
								<td><?php echo lang('terrace_versions');?></td>
								<td class="center">CI <?php echo CI_VERSION; ?></td></tr>
								<tr><td><?php echo lang('scripting_language');?></td>
								<td class="center">PHP <?php echo PHP_VERSION; ?></td>	</tr>
									<tr><td><?php echo lang('data_base');?></td>
								<td class="center"><?php echo $this -> db -> version(); ?></td></tr>
						
						  </tbody>
					  </table>    
					  	<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  <thead>
							  <tr>
								  <th><?php echo lang('manager_info');?></th>
							  </tr>
						  </thead>   
						  <tbody>
							<tr>
								<td><?php echo lang('login_manager');?></td>
								<td class="center"><?php echo $this -> _manager -> username; ?></td>
						</tr>
						<tr>
								<td><?php echo lang('jurisdiction_group');?></td>
								<td class="center"><?php echo $this -> _manager -> rolename; ?></td></tr>
								<td><?php echo lang('all_jurisdiction');?></td>
								<td class="center"><?php echo $this -> _manager -> introduce; ?></td></tr>
								<tr><td><?php echo lang('login_ip');?></td>
								<td class="center"><?php echo $this -> input -> ip_address(); ?></td>	</tr>
								<tr><td><?php echo lang('last_login_time');?></td>
								<td class="center"><?php echo $this -> _manager -> last_log_time == null ? '无' : unix_to_human($this -> _manager -> last_log_time, TRUE, 'eu'); ?></td></tr>
								<tr><td><?php echo lang('now_login_time');?></td>
								<td class="center"><?php echo unix_to_human($this -> _manager -> now_log_time, TRUE, 'eu'); ?></td></tr>
						  </tbody>
					  </table>     
			<div class="clearfix"></div>
		</div>
	</div>
</div><!-- /.内容结束-->
<?php
echo $this -> load -> view('footer');
?>
</body>
</html>