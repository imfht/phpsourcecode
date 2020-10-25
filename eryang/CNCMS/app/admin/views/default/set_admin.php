<?php
echo $this -> load -> view('header');
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li>
			<a href="<?php echo site_url('main'); ?>"><?php echo lang('set_system'); ?></a><span class="divider">/</span>
		</li>
		<li>
			<a href="<?php echo site_url($this -> config -> item('admin_folder') . 'setting/admin');?>"><?php echo $title; ?></a>
		</li>
	</ul>
</div>
<!--头部结束-->
		<?php

		if ($this -> session -> flashdata('message')) {
			$message = $this -> session -> flashdata('message');
		}

		if ($this -> session -> flashdata('error')) {
			$error = $this -> session -> flashdata('error');
		}

		if (function_exists('validation_errors') && validation_errors() != '') {
			$error = validation_errors();
		}
		?>
		
		<?php if (!empty($message)): ?>
		<div class="alert alert-info" >
			<a class="close" data-dismiss="alert">×</a>
			<?php echo $message; ?>
		</div>
	<?php endif; ?>
						<?php if (!empty($error)): ?>
		<div class="alert alert-error" >
			<a class="close" data-dismiss="alert">×</a>
			<?php echo $error; ?>
		</div>
	<?php endif; ?>

<!--基本设置开始-->
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-edit"></i> <?php echo lang('set_admin_basic'); ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
							<?php $attributes = array('class' => 'form-horizontal', 'id' => 'set_admin_basic_form');
			echo form_open($this -> config -> item('admin_folder') . 'setting/admin', $attributes);
		               ?>
		               <input type="hidden" name="tab" value="admin_basic"/>
						  <fieldset>
							<legend><?php echo lang('set_hint'); ?></legend>
							<div class="control-group">
							  <label class="control-label" for="name"><?php echo lang('set_admin_name'); ?></label>
							  <div class="controls">
							  	<?php
								$data = array('id' => 'name', 'name' => 'name', 'value' => set_value('name', SITE_ADMIN_NAME));
								echo form_input($data);
							?>
								<span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_admin_name_span'); ?></span>
							  </div>
							</div>
							
								<div class="control-group">
							  <label class="control-label" for="logo"><?php echo lang('set_admin_logo'); ?> </label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'logo', 'name' => 'logo', 'value' => set_value('logo', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/logo/', '', SITE_ADMIN_LOGO)));
									echo form_input($data);
								?>
								 <span class="help-inline"><?php echo lang('set_admin_logo_span');
									echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default/logo';
									echo lang('set_file');
									echo lang('set_logo_span');
 ?></span>	
							  </div>
							</div>
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary"><?php echo lang('button_save'); ?></button>
							</div>
						  </fieldset>
					</form>
		</div>
	</div><!--/box span12-->
</div>
<!--基本设置结束-->

<!--文件名设置开始-->
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-edit"></i> <?php echo lang('set_filename'); ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
							<?php $attributes = array('class' => 'form-horizontal', 'id' => 'set_filename_form');
			echo form_open($this -> config -> item('admin_folder') . 'setting/admin', $attributes);
		               ?>
		               <input type="hidden" name="tab" value="filename"/>
						  <fieldset>
							<legend><?php echo lang('set_hint'); ?></legend>
								<div class="control-group">
							  <label class="control-label" for="resources"><?php echo lang('set_resources'); ?></label>
							  <div class="controls">
							  	<?php
								$data = array('id' => 'resources', 'name' => 'resources', 'value' => set_value('resources', str_replace('/', '', SITE_ADMIN_RESOURCES)));
								echo form_input($data);
							?>
								<span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_resources_span'); ?></span>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="css"><?php echo lang('set_css'); ?></label>
							  <div class="controls">
							  	<?php
								$data = array('id' => 'css', 'name' => 'css', 'value' => set_value('css', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/', '', SITE_ADMIN_CSS)));
								echo form_input($data);
							?>
								<span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_css_span');
								echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default';
								echo lang('set_file');
								echo lang('set_file_span');
									?></span>
							  </div>
							</div>
							
								<div class="control-group">
							  <label class="control-label" for="js"><?php echo lang('set_js'); ?> </label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'js', 'name' => 'js', 'value' => set_value('js', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/', '', SITE_ADMIN_JS)));
									echo form_input($data);
								?>
								 <span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_js_span');
									echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default';
									echo lang('set_file');
									echo lang('set_file_span');
 ?> </span>
							  </div>
							</div>

                              <div class="control-group">
                                  <label class="control-label" for="img"><?php echo lang('set_img'); ?> </label>
                                  <div class="controls">
                                      <?php
                                      $data = array('id' => 'img', 'name' => 'img', 'value' => set_value('img', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/', '', SITE_ADMIN_IMG)));
                                      echo form_input($data);
                                      ?>
                                      <span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_img_span');
                                          echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default';
                                          echo lang('set_file');
                                          echo lang('set_file_span');
                                          ?> </span>
                                  </div>
                              </div>
							
								<div class="control-group">
							  <label class="control-label" for="editor"><?php echo lang('set_editor'); ?> </label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'editor', 'name' => 'editor', 'value' => set_value('editor', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/', '', SITE_ADMIN_EDITOR)));
									echo form_input($data);
								?>
								 <span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_editor_span');
									echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default';
									echo lang('set_file');
									echo lang('set_file_span');
								  ?> </span>
							  </div>
							</div>
							
																<div class="control-group">
							  <label class="control-label" for="art"><?php echo lang('set_art'); ?> </label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'art', 'name' => 'art', 'value' => set_value('art', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/', '', SITE_ADMIN_ART)));
									echo form_input($data);
								?>
								 <span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_art_span');
									echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default';
									echo lang('set_file');
									echo lang('set_file_span');
								  ?> </span>
							  </div>
							</div>
								<div class="control-group">
							  <label class="control-label" for="valicode"><?php echo lang('set_valicode'); ?> </label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'valicode', 'name' => 'valicode', 'value' => set_value('valicode', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/', '', SITE_ADMIN_VALICODE)));
									echo form_input($data);
								?>
								 <span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_valicode_span');
									echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default';
									echo lang('set_file');
									echo lang('set_file_span');
 ?> </span>
							  </div>
							</div>
							
								<div class="control-group">
							  <label class="control-label" for="uploads"><?php echo lang('set_uploads'); ?></label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'uploads', 'name' => 'uploads', 'value' => set_value('uploads', str_replace(SITE_ADMIN_RESOURCES . '/admin/default/', '', SITE_ADMIN_UPLOADS)));
									echo form_input($data);
								?>
								 <span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_uploads_span');
									echo str_replace('/', '', SITE_ADMIN_RESOURCES) . '/admin/default';
									echo lang('set_file');
									echo lang('set_file_span');
 ?></span>
							  </div>
							</div>
							
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary"><?php echo lang('button_save'); ?></button>
							</div>
						  </fieldset>
					</form>
		</div>
	</div><!--/box span12-->
</div>
<!--文件名设置结束-->

<!--上传大小开始-->
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-edit"></i> <?php echo lang('set_upload_size'); ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
							<?php $attributes = array('class' => 'form-horizontal', 'id' => 'set_upload_size_form');
			echo form_open($this -> config -> item('admin_folder') . 'setting/admin', $attributes);
		               ?>
		               <input type="hidden" name="tab" value="upload"/>
						  <fieldset>
							<legend><?php echo lang('set_hint'); ?></legend>
							<div class="control-group">
							  <label class="control-label" for="upload_image_size"><?php echo lang('set_upload_image_size'); ?></label>
							  <div class="controls">
							  	<?php
								$data = array('id' => 'upload_image_size', 'name' => 'upload_image_size', 'value' => set_value('upload_image_size', SITE_ADMIN_UPLOAD_IMAGE_SIZE));
								echo form_input($data);
							?>
								<span class="help-inline"><?php echo lang('set_upload_image_size_span'); ?></span>
							  </div>
							</div>
							
								<div class="control-group">
							  <label class="control-label" for="upload_flash_size"><?php echo lang('set_upload_flash_size'); ?> </label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'upload_flash_size', 'name' => 'upload_flash_size', 'value' => set_value('upload_flash_size', SITE_ADMIN_UPLOAD_FLASH_SIZE));
									echo form_input($data);
								?>
								 <span class="help-inline"><?php echo lang('set_upload_flash_size_span'); ?> </span>
							  </div>
							</div>
							
								<div class="control-group">
							  <label class="control-label" for="upload_media_size"><?php echo lang('set_uploads_media_size'); ?></label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'upload_media_size', 'name' => 'upload_media_size', 'value' => set_value('upload_media_size', SITE_ADMIN_UPLOAD_MEDIA_SIZE));
									echo form_input($data);
								?>
								 <span class="help-inline"><?php echo lang('set_uploads_media_size_span'); ?></span>
							  </div>
							</div>
							
							<div class="control-group">
							  <label class="control-label" for="upload_file_size"><?php echo lang('set_uploads_file_size'); ?> </label>
							  <div class="controls">
							  		<?php
									$data = array('id' => 'upload_file_size', 'name' => 'upload_file_size', 'value' => set_value('upload_file_size', SITE_ADMIN_UPLOAD_FILE_SIZE));
									echo form_input($data);
								?>
								 <span class="help-inline"><?php echo lang('set_uploads_file_size_span'); ?></span>
							  </div>
							</div>
							
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary"><?php echo lang('button_save'); ?></button>
							</div>
						  </fieldset>
						</form>
		</div>
	</div><!--/box span12-->
</div>
<!--上传大小结束-->

<!--密钥设置开始-->
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-edit"></i> <?php echo lang('set_encryption_key'); ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
							<?php $attributes = array('class' => 'form-horizontal', 'id' => 'set_encryption_key_form');
			echo form_open($this -> config -> item('admin_folder') . 'setting/admin', $attributes);
		               ?>
		               <input type="hidden" name="tab" value="encryption_key"/>
						  <fieldset>
							<legend><?php echo lang('set_hint'); ?></legend>
							<div class="control-group">
							  <label class="control-label" for="encryption_key_begin"><?php echo lang('set_encryption_key_begin'); ?></label>
							  <div class="controls">
							  	<?php
								if (SITE_ADMIN_ENCRYPTION_KEY_BEGIN||SITE_ADMIN_ENCRYPTION_KEY_END) {
									$data = array('id' => 'encryption_key_begin', 'name' => 'encryption_key_begin', 'disabled'=>'disabled','value' => set_value('encryption_key_begin', SITE_ADMIN_ENCRYPTION_KEY_BEGIN));
								} else {
									$data = array('id' => 'encryption_key_begin', 'name' => 'encryption_key_begin', 'value' => set_value('encryption_key_begin', SITE_ADMIN_ENCRYPTION_KEY_BEGIN));
								}
								echo form_input($data);
							?>
								<span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_encryption_key_begin_span'); ?></span>
							  </div>
							</div>
							
								<div class="control-group">
							  <label class="control-label" for="encryption_key_end"><?php echo lang('set_encryption_key_end'); ?></label>
							  <div class="controls">
							  	  	<?php
								if (SITE_ADMIN_ENCRYPTION_KEY_BEGIN||SITE_ADMIN_ENCRYPTION_KEY_END) {
									$data = array('id' => 'encryption_key_end', 'name' => 'encryption_key_end', 'disabled'=>'disabled','value' => set_value('encryption_key_end', SITE_ADMIN_ENCRYPTION_KEY_END));
								} else {
								$data = array('id' => 'encryption_key_end', 'name' => 'encryption_key_end', 'value' => set_value('encryption_key_end', SITE_ADMIN_ENCRYPTION_KEY_END));
								}
								echo form_input($data);
							?>
								<span class="help-inline"><i class="icon-star" title="<?php echo lang('data_required'); ?>"></i><?php echo lang('set_encryption_key_end_span'); ?></span>
							  </div>
							</div>
							
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary"><?php echo lang('button_save'); ?></button>
							</div>
						  </fieldset>
						</form>
		</div>
	</div><!--/box span12-->
</div>
<!--密钥设置结束-->

<!--主题设置开始-->
<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-header well">
			<h2><i class="icon-edit"></i> <?php echo lang('set_theme'); ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
							<?php $attributes = array('class' => 'form-horizontal', 'id' => 'set_theme_form');
			echo form_open($this -> config -> item('admin_folder') . 'setting/admin', $attributes);
		               ?>
		               <input type="hidden" name="tab" value="theme"/>
						  <fieldset>
							<legend><?php echo lang('set_hint'); ?></legend>
							<div class="control-group">
							  <label class="control-label" for="theme"><?php echo lang('set_theme_name'); ?></label>
							  <div class="controls">
							  	<?php
								$data = array('id' => 'theme', 'name' => 'theme', 'value' => set_value('theme', SITE_ADMIN_THEME));
								echo form_input($data);
							?>
								<span class="help-inline"><?php echo lang('set_theme_name_span'); ?></span>
							  </div>
							</div>
							
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary"><?php echo lang('button_save'); ?></button>
							</div>
						  </fieldset>
						</form>
		</div>
	</div><!--/box span12-->
</div>
<!--主题结束-->

<!--邮件设置开始-->
<div class="row-fluid sortable">
    <div class="box span12">
        <div class="box-header well">
            <h2><i class="icon-edit"></i> <?php echo lang('set_email'); ?></h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
            </div>
        </div>
        <div class="box-content">
            <?php $attributes = array('class' => 'form-horizontal', 'id' => 'set_email_form');
            echo form_open($this -> config -> item('admin_folder') . 'setting/admin', $attributes);
            ?>
            <input type="hidden" name="tab" value="email"/>
            <fieldset>
                <legend><?php echo lang('set_hint'); ?></legend>
                <div class="control-group">
                    <label class="control-label"><?php echo lang('set_email_status'); ?></label>
                    <div class="controls">
                        <label class="radio">
                            <input type="radio" id = "email_status" 	 <?php
                            if (SITE_ADMIN_EMAIL_STATUS == 1) { echo 'checked';
                            }
                            ?>
                                   name = "email_status" value ="1" />
                            <?php echo lang('set_status_open');?></label>
                        <div style="clear:both"></div>
                        <label class="radio">
                            <input type="radio" id = "email_status"   <?php
                            if (SITE_ADMIN_EMAIL_STATUS != 1) { echo 'checked';
                            }
                            ?>
                                   name = "email_status" value ="2" />
                            <?php echo lang('set_status_close');?></label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_smtp"><?php echo lang('set_email_smtp'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_smtp', 'name' => 'email_smtp', 'value' => set_value('email_smtp', SITE_ADMIN_EMAIL_SMTP));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_smtp_span'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_port"><?php echo lang('set_email_smtp_port'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_port', 'name' => 'email_port', 'value' => set_value('email_port', SITE_ADMIN_EMAIL_PORT));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_smtp_port_span'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_user"><?php echo lang('set_email_user'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_user', 'name' => 'email_user', 'value' => set_value('email_user', SITE_ADMIN_EMAIL_USER));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_user_span'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_password"><?php echo lang('set_email_password'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_password', 'name' => 'email_password', 'value' => set_value('email_password', $email_password));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_password_span'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_title"><?php echo lang('set_email_title'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_title', 'name' => 'email_title', 'value' => set_value('email_title', SITE_ADMIN_EMAIL_TITLE));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_title_span'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_username"><?php echo lang('set_email_username'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_username', 'name' => 'email_username', 'value' => set_value('email_username', SITE_ADMIN_EMAIL_USERNAME));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_username_span'); ?></span>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo lang('button_save'); ?></button>
                </div>
            </fieldset>
            </form>
        </div>
    </div><!--/box span12-->
</div>
<!--邮件设置结束-->

<!--邮件测试开始-->
<div class="row-fluid sortable">
    <div class="box span12">
        <div class="box-header well">
            <h2><i class="icon-edit"></i> <?php echo lang('set_email_test'); ?></h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
            </div>
        </div>
        <div class="box-content">
            <?php $attributes = array('class' => 'form-horizontal', 'id' => 'set_email_test_form');
            echo form_open($this -> config -> item('admin_folder') . 'setting/admin', $attributes);
            ?>
            <input type="hidden" name="tab" value="email_test"/>
            <fieldset>
                <legend><?php echo lang('set_hint'); ?></legend>
                <div class="control-group">
                    <label class="control-label" for="email_user"><?php echo lang('set_email_user'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_user', 'name' => 'email_user', 'value' => set_value('email_user', SITE_ADMIN_EMAIL_USER));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_user_span'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_to_user"><?php echo lang('set_email_to_user'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_to_user', 'name' => 'email_to_user', 'value' => set_value('email_to_user', $email_to_user));
                        echo form_input($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_to_user_span'); ?></span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="email_content"><?php echo lang('set_email_content'); ?></label>
                    <div class="controls">
                        <?php
                        $data = array('id' => 'email_content', 'name' => 'email_content','rows' => '5', 'value' => set_value('email_content', SITE_ADMIN_EMAIL_CONTENT));
                        echo form_textarea($data);
                        ?>
                        <span class="help-inline"><?php echo lang('set_email_content_span'); ?></span>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo lang('set_email_button_span'); ?></button>
                </div>
            </fieldset>
            </form>
        </div>
    </div><!--/box span12-->
</div>
<!--邮件设置结束-->

<?php
echo $this -> load -> view('footer');
?>
</body>
</html>