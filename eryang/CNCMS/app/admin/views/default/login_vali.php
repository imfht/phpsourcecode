<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<title><?php echo lang('title');?></title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,IE=8,IE=9,chrome=1">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta name="keywords" content="菜鸟CMS"/>
        <meta name="description" content="菜鸟CMS">
        <meta name="author" content="二阳">

		<!--样式-->
		<link id="bs-css" href="/assets/admin/default/css/bootstrap-cerulean.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-bottom: 40px;
			}
			.sidebar-nav {
				padding: 9px 0;
			}
		</style>
		<link href="/assets/admin/default/css/bootstrap-responsive.css" rel="stylesheet">
		<link href="/assets/admin/default/css/charisma-app.css" rel="stylesheet">



        <!--[if lt IE 9]>
        <script src="/assets/admin/default/js/html5.js"></script>
        <![endif]-->
        <!--[if lt IE 9]><!-->
        <script src="/assets/admin/default/js/html5.js"></script>
        <script src="/assets/admin/default/js/respond.min.js"></script>
        <script src="/assets/admin/default/js/excanvas.min.js"></script>
        <!--<!--[endif]-->
        <!--[if lt IE 9]><!-->
        <script src="/assets/admin/default/js/jquery-1.7.2.min.js"></script>
        <!--<!--[endif]-->


        <script type="text/javascript">
            (function () {
                if (navigator.userAgent.indexOf("MSIE 6.") > 0) {
                    location.href = "{:U('Index/oops')}";
                }
                if (navigator.userAgent.indexOf("MSIE 7.") > 0) {
                    location.href = "{:U('Index/oops')}";
                }

            })();
        </script>

		<!--图标-->
		<link rel="shortcut icon" href="/assets/admin/default/img/favicon.ico">
	</head>
	<body>
	<!--标题开始-->
	<div class="row-fluid">
		<div class="span12 center login-header">
			<h2><?php echo lang('title');?></h2>
		</div>
	</div><!--/.标题结束-->
	<!--中间部分开始-->
	<div class="row-fluid">
		<div class="well span5 center login-box">
			<div class="alert alert-info">
				<?php echo lang('login_vali_message');?>
			</div>
				<?php

				if ($this -> session -> flashdata('message')) {
					$message = $this -> session -> flashdata('message');
				}

				if ($this -> session -> flashdata('error')) {
					$error = $this -> session -> flashdata('error');
				}
		?>
		<!--表单开始-->
			<?php $attributes = array('class' => 'form-horizontal', 'name' => 'login_vali_form');
	echo form_open($this -> config -> item('admin_folder') . 'login', $attributes);
			?>
			<fieldset>
				<div class="input-prepend" title="<?php echo lang('login_username');?>" data-rel="tooltip">
					<span class="add-on"><i class="icon-user"></i></span>
					<input autofocus class="input-large span10" name="username" id="username" type="text" value="" />
				</div>
				<div class="clearfix"></div>
				<div class="input-prepend" title="<?php echo lang('login_password');?>" data-rel="tooltip">
					<span class="add-on"><i class="icon-lock"></i></span>
					<?php echo form_password(array('name' => 'password', 'class' => 'input-large span10', 'id' => 'password')); ?>
			</div>
				<div class="clearfix"></div>
				<div class="input-prepend" title="<?php echo lang('message_valicode');?>" data-rel="tooltip">
					<span class="add-on"><i class="icon-tag"></i></span>
						<?php echo form_input(array('name' => 'valicode', 'class' => 'input-large span10', 'id' => 'valicode')); ?>
				</div>
				<div class="clearfix"></div>
				<div class="center" id="vali-code">
					<?php echo $img['image']; ?>
				</div>
				<div class="clearfix"></div>
				<div class="input-prepend">
					<label class="checkbox inline">
					<?php echo form_checkbox(array('name'=>'remember', 'id'=>'remember','value'=>'true'))?>
			<?php echo lang('stay_logged_in'); ?></label>
				</div>
				<div class="clearfix"></div>
			<input type="hidden" value="submitted" name="submitted"/>
				<p class="center span5">
					<button type="submit" class="btn btn-primary">
						<?php echo lang('login');?>
					</button>
				</p>
						<?php if (!empty($message)): ?>
		<div class="alert alert-success" id="success_alert">
			<a class="close" data-dismiss="alert">×</a>
			<?php echo $message; ?>
		</div>
	<?php endif; ?>
				<?php if (!empty($error)): ?>
		<div class="alert alert-error" id="error_alert">
			<a class="close" data-dismiss="alert">×</a>
			<?php echo $error; ?>
		</div>
	<?php endif; ?>
				<div class="alert alert-error" id="alert_error"  style="display: none;"></div>
			</fieldset>
			<?php form_close(); ?>
			<!--/.表单结束-->
            <br/>
            <div style="text-align:center;">
                <a href="<?php echo site_url($this -> config -> item('admin_folder').'login/forgot_password'); ?>"><?php echo lang('forgot_password');?></a>
            </div>
		</div>
	</div>
	<!--/.中间部分结束-->
<hr>
<!--底部开始-->
		<footer>
			<p class="pull-left"><?php echo lang('write_date');?>：2014</p>
			<p class="pull-right"><?php echo lang('writer');?>：<a href="http://weibo.com/513778937?topnav=1&wvr=5" target="_blank">二阳</a></p>
		</footer>
<!--/.底部结束-->
<!--script开始-->
	<script src="/assets/admin/default/js/form.validate.js"></script>
	<script src="/assets/admin/default/js/bootstrap.min.js"></script>
	<script src="/assets/admin/default/js/login.js"></script>
	<script>
												function change_valicode(){
$.get("<?php echo site_url($this -> config -> item('admin_folder') .'login/show_valicode'); ?>", function(data){
	var img_data = $.parseJSON(data);
	$('#vali-code').html(img_data);
	});
	}
	</script>
	<!--script结束-->
	</body>
</html>