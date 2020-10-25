<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('manager'); ?>"><?php echo lang('manager_list'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'manager/form');?>"><?php echo $title; ?></a></li>
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

<!--更换密码开始-->
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header well">
			<h2>
				<i class="icon-edit"></i> <?php echo $title; ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i
					class="icon-chevron-up"></i></a> <a href="#"
					class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
							<?php
							
							$attributes = array (
									'class' => 'form-horizontal',
									'id' => 'manager_pwd_form'
							);
							echo form_open ( $this->config->item ( 'admin_folder' ) . 'manager/pwd', $attributes );
							?>
						  <fieldset>
				<legend><?php echo lang('hint'); ?></legend>

				<div class="control-group">
					<label class="control-label" for="password"><?php echo lang('change_password_now'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'password',
												'name' => 'password',
                                                'placeholder'=>lang('change_password_now').lang('password_length'),
												'value' => set_value ( 'password', '' )
										);
										echo form_password ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('change_password_now_span'); ?></span>
					</div>
				</div>

		<div class="control-group">
					<label class="control-label" for="new_password"><?php echo lang('change_password_new'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'new_password',
												'name' => 'new_password',
                                                'placeholder'=>lang('change_password_now').lang('password_length'),
												'value' => set_value ( 'new_password', '' )
										);
										echo form_password ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
                                                             title="<?php echo lang('data_required'); ?>"></i><?php echo lang('change_password_new_span'); ?></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="new_password_confirm"><?php echo lang('change_password_new_confirm'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'new_password_confirm',
												'name' => 'new_password_confirm',
                                            'placeholder'=>lang('change_password_new_confirm').lang('password_length'),
												'value' => set_value ( 'new_password_confirm', '' )
										);
										echo form_password ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
                                                             title="<?php echo lang('data_required'); ?>"></i><?php echo lang('change_password_new_confirm_span'); ?></span>
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary"><?php echo lang('button_save'); ?></button>
				</div>
			</fieldset>
			</form>
		</div>
	</div>
	<!--/box span12-->
</div>
<!--/row-->
<!--更换密码结束-->
<?php
echo $this->load->view ( 'footer' );
?>
</body>
</html>


