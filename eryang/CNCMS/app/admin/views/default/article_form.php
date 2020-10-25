<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
	<ul class="breadcrumb">
		<li><a href="<?php echo site_url('article'); ?>"><?php echo lang('article_list'); ?></a><span
			class="divider">/</span></li>
		<li><a
			href="<?php echo site_url($this -> config -> item('admin_folder').'article/form');?>"><?php echo $title; ?></a></li>
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

<!--文章开始-->
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
									'id' => 'article_form'
							);
							echo form_open ( $this->config->item ( 'admin_folder' ) . 'article/form/' . $id, $attributes );
							?>
						  <fieldset>
				<legend><?php echo lang('hint'); ?></legend>
				<div class="control-group">
					<label class="control-label" for="article_title"><?php echo lang('article_title'); ?></label>
					<div class="controls">
							  	<?php
										$data = array (
												'id' => 'article_title',
												'name' => 'article_title',
												'value' => set_value ( 'article_title', $article_title )
										);
										echo form_input ( $data );
										?>
								<span class="help-inline"><i class="icon-star"
							title="<?php echo lang('data_required'); ?>"></i><?php echo lang('article_title_span'); ?></span>
					</div>
				</div>

                              <div class="control-group">
                                  <label class="control-label" for="article_content"><?php echo lang('article_content'); ?></label>
                                  <div class="controls">

                                      <?php
                                      $data = array('id' => 'article_content', 'name' => 'article_content', 'value' => set_value('article_content', $article_content));
                                      echo form_textarea($data);
                                      ?>
                                      <div style="clear:both"></div>
                                      <span class="help-inline"><i class="icon-star"
                                                                   title="<?php echo lang('data_required'); ?>"></i><?php echo lang('article_content_span'); ?></span>
                                  </div>
				<?php  if($id):?>
							<div class="control-group">
					<label class="control-label"><?php echo lang('article_status'); ?></label>
					<div class="controls">
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status == 1) {
						echo 'checked';
					}
					?>
							name="status" value="1" /> <?php echo lang('article_status_open'); ?>
						</label>
						<div style="clear: both"></div>
						<label class="radio"> <input type="radio" id="status"
							<?php
					if ($status != 1) {
						echo 'checked';
					}
					?>
							name="status" value="2" /> <?php echo lang('article_status_close'); ?>
						</label>
					</div>
				</div>
				<?php endif;?>

                              <div class="control-group">
                                  <label class="control-label" for="categoryid"><?php echo lang('article_category'); ?></label>
                                  <div class="controls">
                                      <select name="categoryid">
                                          <option value=""><?php echo lang('article_select_category');?></option>
                                          <?php foreach ( $categorys as $v ) :?>
                                              <option value='<?php echo $v->id;?>'
                                                  <?php if($v->id==$categoryid){echo 'selected';}?>><?php echo $v->name;?></option>
                                          <?php endforeach; ?>
                                      </select> <span class="help-inline"><i class="icon-star"
                                                                             title="<?php echo lang('data_required'); ?>"></i><?php echo lang('article_select_category'); ?></span>
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
<!--文章结束-->
<?php
echo $this->load->view ( 'footer' );
?>
<script type="text/javascript" charset="utf-8" src="<?php echo SITE_ADMIN_EDITOR; ?>/kindeditor-all-min.js"></script>
<script type="text/javascript">
    var editor;
    KindEditor.ready(function (K){
        var options =
        {
            width : '860px',
            height : '380px',
            fileManagerJson : '<?php echo site_url('common/editor_manager'); ?>',
            uploadJson : '<?php echo site_url('common/upload'); ?>',
            allowFileManager : true
        };
        editor = K.create('#article_content', options);
    });


    $("#article_form").validate({
        rules : {
            article_title:{
                required: true,
                chinese : "只能包含中文、英文字母、数字、下划线、破折号",
                maxlength : 50
            },
            article_content:{
                required: true,
                minlength : 10
            },
            status:{
                required: true
            },
            categoryid:{
                required: true
            }
        },
        messages : {
            article_title:{
                required : "<i class='icon-remove'></i>必须填写",
                alpha_dash_bias : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
                maxlength : "<i class='icon-remove'></i>最大长度50"
            },
            article_content:{
                required : "<i class='icon-remove'></i>必须填写",
                minlength : "<i class='icon-remove'></i>最小长度10"
            },
            status:{
                required : "<i class='icon-remove'></i>必须选择"
            },
            categoryid:{
                required : "<i class='icon-remove'></i>必须选择"
            }
        },
        errorElement : "span",
        highlight : function(element, errorClass) {// element出错时触发
            $(element).parent().parent().removeClass('success');
            $(element).parent().parent().addClass('error');
        },
        unhighlight : function(element, errorClass) {// element通过验证时触发
            $(element).parent().parent().removeClass('error');
            $(element).parent().parent().addClass('success');
        },
        errorPlacement : function(error, element) {
            $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
            error.appendTo(element.next());
            element.parent().parent().effect('shake', {
                times : 2
            }, 100);
        },
        submitHandler : function(form) {
            editor.sync();
            form.submit();
        }
    });

</script>
</body>
</html>


