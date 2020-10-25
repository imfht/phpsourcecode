<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('category'); ?>"><?php echo lang('category_list'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'category/form');?>"><?php echo $title; ?></a></li>
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

<!--类别开始-->
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
									'id' => 'category_form' 
							);
							echo form_open ( $this->config->item ( 'admin_folder' ) . 'category/form/' . $id, $attributes );
							?>
						  <fieldset>
				<legend><?php echo lang('hint'); ?></legend>
				<div class="control-group">
					<label class="control-label" for="name"><?php echo lang('category_name'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'name',
												'name' => 'name',
												'value' => set_value ( 'name', $name ) 
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('category_name_span'); ?></span>
					</div>
				</div>

                              <?php  if($pid!=0):?>
                              <div class="control-group">
                                  <label class="control-label"><?php echo lang('category_type'); ?></label>
                                  <div class="controls">
                                      <label class="radio"> <input type="radio" id="type"
                                              <?php
                                              if ($type == 1) {
                                                  echo 'checked';
                                              }
                                              ?>
                                                                   name="type" value="1" /> <?php echo lang('category_type_list'); ?>
                                      </label>
                                      <div style="clear: both"></div>
                                      <label class="radio"> <input type="radio" id="type"
                                              <?php
                                              if ($type == 2) {
                                                  echo 'checked';
                                              }
                                              ?>
                                                                   name="type" value="2" /> <?php echo lang('category_type_page'); ?>
                                      </label>
                                  </div>
                              </div>
                              <?php endif;?>
                <?php  if($id):?>
				<div class="control-group">
					<label class="control-label" for="rank"><?php echo lang('category_rank'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'rank',
												'name' => 'rank',
												'value' => set_value ( 'rank', $rank )
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><?php echo lang('category_rank_span'); ?></span>
					</div>
				</div>
                <?php endif;?>
				<?php  if($id):?>
							<div class="control-group">
					<label class="control-label"><?php echo lang('category_status'); ?></label>
					<div class="controls">
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status == 1) {
						echo 'checked';
					}
					?>
							name="status" value="1" /> <?php echo lang('category_status_open'); ?>
						</label>
						<div style="clear: both"></div>
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status != 1) {
						echo 'checked';
					}
					?>
							name="status" value="2" /> <?php echo lang('category_status_close'); ?>
						</label>
					</div>
				</div>
				<?php endif;?>

                              <div class="control-group">
                                  <label class="control-label" for="pid"><?php echo lang('category_pid'); ?></label>
                                  <div class="controls">
                                      <select name="pid">
                                          <option value=""><?php echo lang('category_select_none');?></option>
                                          <?php foreach ( $pname as $v ) :?>
                                              <option value='<?php echo $v->id;?>'
                                                  <?php if($v->id==$pid){echo 'selected';}?>><?php echo $v->name;?></option>
                                          <?php endforeach; ?>
                                      </select> <span class="help-inline"><?php echo lang('category_pid_span'); ?></span>
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
<!--类别结束-->
<?php
echo $this->load->view ( 'footer' );
?>
</body>
</html>


