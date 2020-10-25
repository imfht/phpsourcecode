<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('slide'); ?>"><?php echo lang('slide_list'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'slide/form');?>"><?php echo $title; ?></a></li>
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

<!--首页幻灯片开始-->
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
									'id' => 'slide_form'
							);
							echo form_open ( $this->config->item ( 'admin_folder' ) . 'slide/form/' . $id, $attributes );
							?>
						  <fieldset>
				<legend><?php echo lang('hint'); ?></legend>
				<div class="control-group">
					<label class="control-label" for="title"><?php echo lang('slide_title'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'title',
												'name' => 'title',
												'value' => set_value ( 'title', $slide_title )
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('slide_title_span'); ?></span>
					</div>
				</div>
                              <div class="control-group">
                                  <label class="control-label" for="url"><?php echo lang('slide_url'); ?></label>
                                  <div class="controls">
                                      <?php
                                      $data = array (
                                          'id' => 'url',
                                          'name' => 'url',
                                          'value' => set_value ( 'url', $slide_url )
                                      );
                                      echo form_input ( $data );
                                      ?>
                                      <span class="help-inline"><?php echo lang('slide_url_span'); ?></span>
                                  </div>
                              </div>

                              <div class="control-group">
                                  <label class="control-label" for="rank"><?php echo lang('slide_rank'); ?></label>
                                  <div class="controls">
                                      <?php
                                      $data = array (
                                          'id' => 'rank',
                                          'name' => 'rank',
                                          'value' => set_value ( 'rank', $slide_rank )
                                      );
                                      echo form_input ( $data );
                                      ?>
                                      <span class="help-inline"><?php echo lang('slide_rank_span'); ?></span>
                                  </div>
                              </div>

                              <div class="control-group">
                                  <label class="control-label" for="remark"><?php echo lang('slide_remark'); ?></label>
                                  <div class="controls">

                                      <?php
                                      $data = array('id' => 'remark', 'name' => 'remark', 'value' => set_value('remark', $slide_remark));
                                      echo form_textarea($data);
                                      ?>
                                      <div style="clear:both"></div>
                                      <span class="help-inline"><?php echo lang('slide_remark_span'); ?></span>
                                  </div>
				<?php  if($id):?>
							<div class="control-group">
					<label class="control-label"><?php echo lang('slide_status'); ?></label>
					<div class="controls">
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status == 1) {
						echo 'checked';
					}
					?>
							name="status" value="1" /> <?php echo lang('slide_status_open'); ?>
						</label>
						<div style="clear: both"></div>
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status != 1) {
						echo 'checked';
					}
					?>
							name="status" value="2" /> <?php echo lang('slide_status_close'); ?>
						</label>
					</div>
				</div>
				<?php endif;?>

                              <div class="control-group">
                                  <label class="control-label" for="mode_id"><?php echo lang('slide_mode'); ?></label>
                                  <div class="controls">
                                      <select name="mode_id">
                                          <option value=""><?php echo lang('slide_mode_span');?></option>
                                          <?php foreach ( $modes as $v ) :?>
                                              <option value='<?php echo $v->id;?>'
                                                  <?php if($v->id==$mode_id){echo 'selected';}?>><?php echo $v->name;?></option>
                                          <?php endforeach; ?>
                                      </select> <span class="help-inline"><i class="icon-star"
                                                                             title="<?php echo lang('data_required'); ?>"></i><?php echo lang('slide_select_mode'); ?></span>
                                  </div>
                              </div>
                                  <div class="control-group">
                                      <label class="control-label" for="thumb"><?php echo lang('slide_thumb'); ?></label>
                                      <div class="controls">

                                          <img class="grayscale" src="<?php echo $slide_thumb != null ? $slide_thumb : '/assets/admin/default/img/nopic.gif';?>"
                                               onclick="uploadpic(this,'thumb')" width="150" id="imgthumb"
                                              />
                                          <input type="hidden" name="thumb" id="thumb" value="<?php echo $slide_thumb; ?>">

                                          <span class="help-inline"><?php echo lang('slide_thumb_span'); ?></span>
                                      </div>


                                  </div>

                                  <div class="control-group">
                                      <label class="control-label"></label>
                                      <div class="controls">
                                          <input type="button" class="btn" onclick="unsetThumb('thumb','imgthumb')" value="<?php echo lang('slide_nopic_btn_span'); ?>" />
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
<!--首页幻灯片结束-->
<?php
echo $this->load->view ( 'footer' );
?>
<link href="<?php echo SITE_ADMIN_EDITOR; ?>/themes/default/default.css" rel="stylesheet">
<script type="text/javascript" charset="utf-8" src="<?php echo SITE_ADMIN_EDITOR; ?>/kindeditor-all-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo SITE_ADMIN_EDITOR; ?>/lang/zh_CN.js"></script>
<script type="text/javascript">
    function uploadpic(t,picid){
        var editor = KindEditor.editor({
            fileManagerJson:'<?php echo site_url('common/editor_manager'); ?>',
            uploadJson:'<?php echo site_url('common/upload'); ?>',
            allowFileManager : true
        });
        editor.loadPlugin('image', function() {
            editor.plugin.imageDialog({
                imageUrl : KindEditor('#'+picid).val(),
                clickFn : function(url, title, width, height, border, align) {
                    $('#'+picid).val(url);
                    if(t){
                        $(t).attr('src',url);
                    }
                    editor.hideDialog();
                }
            });
        });
    }

    // ------------------------------------------------------------------------

    function unsetThumb(objid,imgobjid){

        $("#"+objid).val('');
        $("#"+imgobjid).attr('src','/assets/admin/default/img/nopic.gif');
    }

    // ------------------------------------------------------------------------
</script>
</body>
</html>


