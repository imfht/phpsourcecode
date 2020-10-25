<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('role'); ?>"><?php echo lang('role_list'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'role/form');?>"><?php echo $title; ?></a></li>
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

<!--角色开始-->
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
									'id' => 'role_form' 
							);
							echo form_open ( $this->config->item ( 'admin_folder' ) . 'role/form/' . $id, $attributes );
							?>
						  <fieldset>
				<legend><?php echo lang('hint'); ?></legend>
				<div class="control-group">
					<label class="control-label" for="name"><?php echo lang('role_name'); ?></label>
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
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('role_name_span'); ?></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="introduce"><?php echo lang('role_introduce'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'introduce',
												'name' => 'introduce',
												'value' => set_value ( 'introduce', $introduce ) 
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('role_introduce_span'); ?></span>
					</div>
				</div>

				<?php  if($status):?>
							<div class="control-group">
					<label class="control-label"><?php echo lang('role_status'); ?></label>
					<div class="controls">
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status == 1) {
						echo 'checked';
					}
					?>
							name="status" value="1" /> <?php echo lang('role_status_open'); ?>
						</label>
						<div style="clear: both"></div>
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status != 1) {
						echo 'checked';
					}
					?>
							name="status" value="2" /> <?php echo lang('role_status_close'); ?>
						</label>
					</div>
				</div>
				<?php endif;?>
				<table
					class="table table-striped table-bordered bootstrap-datatable datatable">
					<thead>
						<tr>
							<th><?php echo lang('role_powers_span');?></th>
						</tr>
					</thead>
					<tbody>
 <?php foreach($power_datas as $data): ?>
    <tr>
							<td style="text-align: center; vertical-align: middle;"><input
								class="input_checkbox" type="checkbox" id="powers[]"
								name="powers[]" value="<?php echo $data['id']; ?>"
								onClick="power_click(this, <?php echo $data['id']; ?>);" /><?php echo $data['name']; ?><?php if(isset($data['children_datas'])): ?><span
								class="icon icon-arrowthick-e" /> <?php endif; ?></td>
							<td><span id="dj_<?php echo $data['id']; ?>">
        <?php if(isset($data['children_datas'])): foreach($data['children_datas'] as $n => $child_data): ?>
        <?php if($child_data['level'] == 1): ?>
        <div><?php endif; ?><input class="input_checkbox"
											type="checkbox" id="powers[]" name="powers[]"
											value="<?php echo $child_data['id']; ?>" disabled="disabled"
											onClick="power_click(this, <?php echo $child_data['id']; ?>);" />
										<span class="help-inline" style="width: 80px;"><?php echo $child_data['name']; ?></span> <?php if(isset($child_data['last_one']) && $child_data['level'] != 1): ?>
        
							
							
							
							
							
							
							
							
							
							</span>
        <?php if(isset($data['children_datas'][$n + 1]) && $data['children_datas'][$n + 1]['level'] == 1): ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php if(isset($child_data['have_child'])): ?>
        <span id="dj_<?php echo $child_data['id']; ?>">
        <?php endif; ?>
        <?php if(!isset($data['children_datas'][$n + 1])): ?>
        </div>
							</span>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?></td>
						</tr>
    <?php endforeach; ?> 	
					  </tbody>
				</table>

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
<!--角色结束-->
<?php
echo $this->load->view ( 'footer' );
?>
<script type="text/javascript"
	src="<?php echo SITE_ADMIN_JS; ?>/lib_ajax_cate.js"></script>
<?php
if ($id || $powers) :
	?>
<script type="text/javascript">
default_sel('<?php echo $powers; ?>');
</script>
<?php endif;?>
</body>
</html>


