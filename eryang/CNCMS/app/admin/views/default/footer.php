			</div>	<!-- /.右边内容结束 -->
		</div><!--/.中间部分结束-->
		<hr>
		<!-- 底部开始 -->
		<!--修改密码模拟窗口开始-->
		<div class="modal hide fade" id="myModal">
					<!--表单开始-->
		<?php $attributes = array('class' => 'form-horizontal', 'name' => 'change_password_form','id'=>'change_password_form');
			echo form_open($this -> config -> item('admin_folder') . 'login', $attributes);
		?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3><?php echo lang('change_password');?></h3>
			</div> 
			<div class="modal-body" id="modal_body">
		 <div class="control-group">
    <label class="control-label" for="password"><?php echo lang('change_password_now');?></label>
    <div class="controls">
     
    	  	<?php
				$data = array('id' => 'password', 'name' => 'password','placeholder'=>lang('change_password_now').lang('password_length'), 'value' => set_value('password', ''));
				echo form_password($data);
							?>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="new_password"><?php echo lang('change_password_new');?></label>
    <div class="controls">
     	<?php
				$data = array('id' => 'new_password', 'name' => 'new_password','placeholder'=>lang('change_password_new').lang('password_length'), 'value' => set_value('new_password', ''));
				echo form_password($data);
							?></div>
  </div>
    <div class="control-group">
    <label class="control-label" for="new_password_confirm"><?php echo lang('change_password_new_confirm');?></label>
    <div class="controls">
   <?php
				$data = array('id' => 'new_password_confirm', 'name' => 'new_password_confirm','placeholder'=>lang('change_password_new_confirm').lang('password_length'), 'value' => set_value('new_password_confirm', ''));
				echo form_password($data);
							?></div>
  </div>
			<div class="alert alert-error" id="alert_error" style="display: none;">
			</div>
	
	
		<!--/.表单结束-->
			</div>
			<div class="modal-footer" id="modal_footer">
				<a  class="btn btn-primary" href="javascript:void(0);" onclick="change_pwd();"><?php echo lang('button_save');?></a>
				<a href="#" class="btn" data-dismiss="modal"><?php echo lang('button_close');?></a>
			</div>
				<?php form_close(); ?>
		</div>
		<!--修改密码模拟窗口结束-->

		<div class="modal hide fade" id="myModal_success">	
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3><?php echo lang('change_password');?></h3>
			</div> 
			<div class="modal-body" >
				<?php echo lang('change_password_succ_logout');?></div>
			<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo lang('button_close');?></a>
			</div>
		</div>
		<footer>
			<p class="pull-left"><?php echo lang('write_date');?>：<?php //echo date('Y') ?>2014</p>
			<p class="pull-right"><?php echo lang('writer');?>：<a href="http://weibo.com/513778937?topnav=1&wvr=5" target="_blank">二阳</a></p>
		</footer>
	<!--/底部结束-->
</div>
	
	<!-- script开始-->
	<!-- jQuery -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery-1.7.2.min.js"></script>
	<!-- jQuery UI -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery-ui-1.8.21.custom.min.js"></script>
	<!-- transition / effect library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-transition.js"></script>
	<!-- alert enhancer library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-alert.js"></script>
	<!-- modal / dialog library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-modal.js"></script>
	<!-- custom dropdown library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-dropdown.js"></script>
	<!-- scrolspy library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-scrollspy.js"></script>
	<!-- library for creating tabs -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-tab.js"></script>
	<!-- library for advanced tooltip -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-tooltip.js"></script>
	<!-- popover effect library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-popover.js"></script>
	<!-- button enhancer library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-button.js"></script>
	<!-- accordion library (optional, not used in demo) -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-collapse.js"></script>
	<!-- carousel slideshow library (optional, not used in demo) -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-carousel.js"></script>
	<!-- autocomplete library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-typeahead.js"></script>
	<!-- tour library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/bootstrap-tour.js"></script>
	<!-- library for cookie management -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.cookie.js"></script>
	<!-- calander plugin -->
	<script src="<?php echo SITE_ADMIN_JS;?>/fullcalendar.min.js"></script>
	<!-- data table plugin -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.dataTables.min.js"></script>

	<!-- chart libraries start -->
	<script src="<?php echo SITE_ADMIN_JS;?>/excanvas.js"></script>
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.flot.min.js"></script>
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.flot.pie.min.js"></script>
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.flot.stack.js"></script>
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.flot.resize.min.js"></script>
	<!-- chart libraries end -->

	<!-- select or dropdown enhancer -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.chosen.min.js"></script>
	<!-- checkbox, radio, and file input styler -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.uniform.min.js"></script>
	<!-- plugin for gallery image view -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.colorbox.min.js"></script>
	<!-- rich text editor library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.cleditor.min.js"></script>
	<!-- notification plugin -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.noty.js"></script>
	<!-- file manager library -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.elfinder.min.js"></script>
	<!-- star rating plugin -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.raty.min.js"></script>
	<!-- for iOS style toggle switch -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.iphone.toggle.js"></script>
	<!-- autogrowing textarea plugin -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.autogrow-textarea.js"></script>
	<!-- multiple file upload plugin -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.uploadify-3.1.min.js"></script>
	<!-- history.js for cross-browser state change on ajax -->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.history.js"></script>
	<!-- application script for Charisma demo -->
	<script src="<?php echo SITE_ADMIN_JS;?>/charisma.js"></script>
	<!--回到顶部-->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.scrollUp.min.js"></script>
	<!--钉元素-->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.pin.js"></script>
	<!--表单验证-->
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.validation.js"></script>
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.validation.additional-methods.js"></script>
	<script src="<?php echo SITE_ADMIN_JS;?>/jquery.validation.message_cn.js"></script>
	<!--弹窗-->
	<script src="<?php echo SITE_ADMIN_ART;?>/jquery.artDialog.min.js"></script>
	<script src="<?php echo SITE_ADMIN_ART;?>/artDialog.plugins.min.js"></script>
	<script src="<?php echo SITE_ADMIN_JS;?>/main.js"></script>
	 
	<script type="text/javascript">
     $(document).ready(function(){
      		var current_theme = "<?php if ($this -> _manager) {echo $this -> _manager -> skin;} else {echo 'cerulean';}?>";
					switch_theme(current_theme);
					$('#themes a[data-value="' + current_theme + '"]').find('i').addClass('icon-ok');
			$('#themes a').click(function(e) {	
					e.preventDefault();
					current_theme = $(this).attr('data-value');
					$.post("<?php echo site_url($this -> config -> item('admin_folder') . 'login/show_skin'); ?>",{skin : current_theme});
			
			switch_theme(current_theme);
			$('#themes i').removeClass('icon-ok');
			$(this).find('i').addClass('icon-ok');
			});
			// ------------------------------------------------------------------------

			function switch_theme(theme_name) {
			$('#bs-css').attr('href', '<?php echo SITE_ADMIN_CSS;?>/bootstrap-' + theme_name + '.css');
			}
			// ------------------------------------------------------------------------

			});
	// ------------------------------------------------------------------------
	// 获取当前时间
	function get_time() {
		var date = new Date();
		$('#local_time').html(date.toLocaleString());
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 更换密码
	 */
	function change_pwd(){
		var password=$('#password').val();
		var new_password=$('#new_password').val();
		var new_password_confirm=$('#new_password_confirm').val();
	$.post("<?php echo site_url($this -> config -> item('admin_folder') .'manager/change_pwd'); ?>",{password : password,new_password : new_password,new_password_confirm : new_password_confirm}, function(data){
	var message_data = $.parseJSON(data);
	if(message_data['error']==9){
		$('#myModal').modal('hide');
		$('#myModal_success').modal('show');
        setTimeout('change_pwd_logout()', 1000);
	}else{
	$('#alert_error').html(message_data['error_msg']);
	$('#alert_error').show();
	}
	});
	}

     function change_pwd_logout(){
         $.post("<?php echo site_url($this -> config -> item('admin_folder') . 'manager/change_pwd_logout');?>");
         setTimeout('top.window.location="<?php echo site_url($this -> config -> item('admin_folder') . 'login');?>";', 1000);
     }

	// ------------------------------------------------------------------------
	</script>
	<!-- script结束-->
	<!-- 回到顶部-->
	<a id="scrollUp" href="#top" title="" style="position: fixed; z-index: 2147483647; display: none; "></a>