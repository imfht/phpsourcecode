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

<!--管理员开始-->
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
									'id' => 'manager_form' 
							);
							echo form_open ( $this->config->item ( 'admin_folder' ) . 'manager/form/' . $id, $attributes );
							?>
						  <fieldset>
				<legend><?php echo lang('hint'); ?></legend>

				<div class="control-group">
					<label class="control-label" for="role_id"><?php echo lang('manager_manager_role_id'); ?></label>
					<div class="controls">
						<select name="role_id" id="role_id">
                            <option value=""><?php echo lang('manager_select_none');?></option>
 				<?php foreach ( $roles as $v ) :?>
							<option value='<?php echo $v['id'];?>'
								<?php if($v['id']==$role_id){echo 'selected';}?>><?php echo $v['name'];?></option>
 				<?php endforeach; ?>
				</select> <span class="help-inline"><?php echo lang('manager_manager_role_id_span'); ?></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="username"><?php echo lang('manager_username'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'username',
												'name' => 'username',
												'value' => set_value ( 'username', $username ) 
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('manager_username_span'); ?></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="password"><?php echo lang('manager_password'); ?></label>
					<div class="controls">
							  	<?php
					$data = array (
							'id' => 'password',
							'name' => 'password',
							'value' => set_value ( 'password', '' )
					);
					echo form_password ( $data );
					?>
								<span class="help-inline"><?php echo lang('manager_password_span'); ?></span>
					</div>
				</div>

                              <div class="control-group">
                                  <label class="control-label" for="password_confirm"><?php echo lang('manager_password_confirm'); ?></label>
                                  <div class="controls">
                                      <?php
                                      $data = array (
                                          'id' => 'password_confirm',
                                          'name' => 'password_confirm',
                                          'value' => set_value ( 'password_confirm', '' )
                                      );
                                      echo form_password ( $data );
                                      ?>
                                      <span class="help-inline"><?php echo lang('manager_password_confirm_span'); ?></span>
                                  </div>
                              </div>

		<div class="control-group">
					<label class="control-label" for="nickname"><?php echo lang('manager_nickname'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'nickname',
												'name' => 'nickname',
												'value' => set_value ( 'nickname', $nickname ) 
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><?php echo lang('manager_nickname_span'); ?></span>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="phone"><?php echo lang('manager_phone'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'phone',
												'name' => 'phone',
												'value' => set_value ( 'phone', $phone ) 
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><?php echo lang('manager_phone_span'); ?></span>
					</div>
				</div>

                              <div class="control-group">
                                  <label class="control-label"><?php echo lang('phone_status'); ?></label>
                                  <div class="controls">
                                      <label class="radio"> <input type="radio" id="phone_status"
                                              <?php
                                              if ($phone_status == 1) {
                                                  echo 'checked';
                                              }
                                              ?>
                                                                   name="phone_status" value="1" /><?php  echo lang('phone_status_open_span');?>
                                      </label>
                                      <div style="clear: both"></div>
                                      <label class="radio"> <input type="radio" id="phone_status"
                                              <?php
                                              if ($phone_status != 1) {
                                                  echo 'checked';
                                              }
                                              ?>
                                                                   name="phone_status" value="2" /> <?php  echo lang('phone_status_close_span');?>
                                      </label>
                                  </div>
                              </div>
				<div class="control-group">
					<label class="control-label" for="email"><?php echo lang('manager_email'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'email',
												'name' => 'email',
												'value' => set_value ( 'email', $email ) 
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('manager_email_span'); ?></span>
					</div>
				</div>


                                  <div class="control-group">
                                      <label class="control-label"><?php echo lang('email_status'); ?></label>
                                      <div class="controls">
                                          <label class="radio"> <input type="radio" id="email_status"
                                                  <?php
                                                  if ($email_status == 1) {
                                                      echo 'checked';
                                                  }
                                                  ?>
                                                                       name="email_status" value="1" /> <?php  echo lang('email_status_open_span');?>
                                          </label>
                                          <div style="clear: both"></div>
                                          <label class="radio"> <input type="radio" id="email_status"
                                                  <?php
                                                  if ($email_status != 1) {
                                                      echo 'checked';
                                                  }
                                                  ?>
                                                                       name="email_status" value="2" /> <?php  echo lang('email_status_close_span');?>
                                          </label>
                                      </div>
                                  </div>


				<?php  if($status):?>
							<div class="control-group">
					<label class="control-label"><?php echo lang('manager_status'); ?></label>
					<div class="controls">
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status == 1) {
						echo 'checked';
					}
					?>
							name="status" value="1" /> <?php  echo lang('manager_status_open_span');?>
						</label>
						<div style="clear: both"></div>
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status != 1) {
						echo 'checked';
					}
					?>
							name="status" value="2" /> <?php  echo lang('manager_status_close_span');?>
						</label>
					</div>
				</div>
				<?php endif;?>

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
<!--管理员结束-->
<?php
echo $this->load->view ( 'footer' );
?>
</body>
</html>


