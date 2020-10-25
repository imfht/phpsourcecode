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

        if ($this -> session -> flashdata('error')) {
            $error = $this -> session -> flashdata('error');
        }

        if (function_exists ( 'validation_errors' ) && validation_errors () != '') {
            $error = validation_errors ();
        }

        ?>
        <!--表单开始-->
        <?php $attributes = array('class' => 'form-horizontal', 'name' => 'forgot_password_form');
        echo form_open($this -> config -> item('admin_folder') . 'login/forgot_password', $attributes);
        ?>
        <fieldset>
            <!--标题开始-->
            <div class="row-fluid">
                <div class="span12 center login-header">
                    <h2><?php echo $title; ?></h2>
                </div>
            </div><!--/.标题结束-->
            <div class="control-group">
                <label class="control-label" for="username"><?php echo lang('login_username');?></label>
                <div class="controls">
                    <?php
                    $data = array (
                        'id' => 'username',
                        'name' => 'username',
                        'value' => set_value ( 'username', $username ),
                        'class'=>'input-large span10',
                        'autofocus'=>'autofocus'
                    );
                    echo form_input ( $data );
                    ?>

                </div>
            </div>
            <input type="hidden" value="submitted" name="submitted"/>
            <p class="center span3">
                <button type="submit" class="btn btn-primary">
                    <?php echo lang('next_button'); ?>
                </button>
            </p>
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
            <a href="<?php echo site_url($this -> config -> item('admin_folder').'login'); ?>"><?php echo lang('return_login');?></a>
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
