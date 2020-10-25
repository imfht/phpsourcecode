<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo lang('title'); ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,IE=8,IE=9,chrome=1">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta name="keywords" content="菜鸟CMS"/>
    <meta name="description" content="菜鸟CMS">
    <meta name="author" content="二阳">
    <style type="text/css">
        body {
            padding-bottom: 40px;
        }
        .sidebar-nav {
            padding: 9px 0;
        }
    </style>
    <!--样式-->
    <link href="/assets/admin/default/css/bootstrap-cerulean.css" rel="stylesheet">
    <link href="/assets/admin/default/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/assets/admin/default/css/charisma-app.css" rel="stylesheet">


    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="/assets/admin/default/js/html5.js"></script>
    <![endif]-->
    <!--图标-->
    <link rel="shortcut icon" href="/assets/admin/default/img/favicon.ico">
</head>
<body>
<div class="row-fluid">
    <div class="well span5 center login-box">
        <?php

        if ($this -> session -> flashdata('message')) {
            $message = $this -> session -> flashdata('message');
        }

        if ($this -> session -> flashdata('error')) {
            $error = $this -> session -> flashdata('error');
        }

        if (function_exists ( 'validation_errors' ) && validation_errors () != '') {
            $error = validation_errors ();
        }
        ?>
        <!--表单开始-->
        <?php $attributes = array('class' => 'form-horizontal', 'name' => 'check_username_form');
        echo form_open($this -> config -> item('admin_folder') . 'login/check_username', $attributes);
        ?>
        <fieldset>
            <!--标题开始-->
            <div class="row-fluid">
                <div class="span12 center login-header">
                    <h2><?php echo $title; ?></h2>
                </div>
            </div><!--/.标题结束-->
            <div class="control-group">
                <label class="control-label"><?php echo lang('login_username');?></label>
                <div class="controls">
                    <span class="span1"><?php echo $username;?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('username_to_email');?></label>
                <div class="controls">
                    <span class="span1"><?php echo $email;?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="register_email"><?php echo lang('register_email');?></label>
                <div class="controls">
                    <?php
                    $data = array (
                        'id' => 'register_email',
                        'name' => 'register_email',
                        'value' => set_value ( 'register_email', $register_email ),
                        'class'=>'input-large span10',
                        'autofocus'=>'autofocus'
                    );
                    echo form_input ( $data );
                    ?>

                </div>
            </div>
            <input type="hidden" value="submitted" name="submitted"/>
            <p class="center span4">
                <button type="submit" class="btn btn-primary">
                    <?php echo $btn_span; ?>
                </button>
            </p>
            <?php if (!empty($message)): ?>
                <div class="alert alert-success" id="success_alert">
                    <a class="close" data-dismiss="alert">×</a>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <a class="close" data-dismiss="alert" id="error_alert">×</a>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <div class="alert alert-error" id="alert_error" style="display: none;">
            </div>
        </fieldset>
        <?php form_close(); ?>
        <!--/.表单结束-->
        <br/>
        <div style="text-align:center;">
            <a href="<?php echo site_url($this -> config -> item('admin_folder').'login/forgot_password'); ?>"><?php echo lang('return_one');?></a>
        </div>
    </div>
</div>
<hr>
<!--底部开始-->
<footer>
    <p class="pull-left"><?php echo lang('write_date'); ?>：<?php echo date('Y') ?></p>
    <p class="pull-right"><?php echo lang('writer'); ?>：<a href="http://weibo.com/513778937?topnav=1&wvr=5" target="_blank">二阳</a></p>
</footer>
<!--/.底部结束-->
<!--script开始-->
<script src="/assets/admin/default/js/jquery-1.7.2.min.js"></script>
<script src="/assets/admin/default/js/form.validate.js"></script>
<script src="/assets/admin/default/js/bootstrap.min.js"></script>
<script src="/assets/admin/default/js/login.js"></script>
<!--/.script结束-->
</body>
</html>
