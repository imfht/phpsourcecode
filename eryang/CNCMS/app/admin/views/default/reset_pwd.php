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

        <fieldset>
            <!--标题开始-->
            <div class="row-fluid">
                <div class="span12 center login-header">
                    <h2><?php echo $title; ?></h2>
                </div>
            </div><!--/.标题结束-->
            <?php if($check):?>

             <div class="span12">
                <div class="content">
                    <p><?php echo lang('fail_url');?></p>
                </div>
            </div>
            <div class="center span4">
               <?php echo lang('click');?> <a href="<?php echo site_url($this -> config -> item('admin_folder').'login/forgot_password');?>" target="_self">
                    <?php echo lang('reapply'); ?></a> <?php echo lang('or');?><a href="<?php echo site_url($this -> config -> item('admin_folder').'login');?>" target="_self">
                    <?php echo lang('return_index_page'); ?></a>
            </div>
            <hr/>
            <?php else: ?>
            <?php
            $attributes = array (
                'class' => 'form-horizontal',
                'id' => 'reset_pwd_form'
            );
                echo form_open ( $this->config->item ( 'admin_folder' ) . 'login/reset_new_pwd', $attributes );
                ?>
                <div class="center span10">
                    <span><?php echo lang('login_username'); ?>:</span>
                    <span><b><?php echo $username; ?></b></span>
                </div>
                <br/>
                <div class="center span10">
                    <div class="control-group">
                        <label class="control-label" for="new_password"><?php echo lang('new_password'); ?></label>
                        <div class="controls">
                            <?php
                            $data = array (
                                'id' => 'new_password',
                                'name' => 'new_password',
                                'value' => set_value ( 'new_password', '' )
                            );
                            echo form_password ( $data );
                            ?>
                            <span class="help-inline"><?php echo lang('password_length_span'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="center span10">
                    <div class="control-group">
                        <label class="control-label" for="new_password_fit"><?php echo lang('new_password_fit'); ?></label>
                        <div class="controls">
                            <?php
                            $data = array (
                                'id' => 'new_password_fit',
                                'name' => 'new_password_fit',
                                'value' => set_value ( 'new_password_fit', '' )
                            );
                            echo form_password ( $data );
                            ?>
                            <span class="help-inline"><?php echo lang('password_length_span'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="center span10">
                    <input type="hidden" value="submitted" name="submitted"/>
                    <input type="hidden" value="<?php echo $submit_url;?>" name="submit_url"/>
                    <button type="submit" class="btn btn-primary">
                        <?php echo lang('finish'); ?>
                    </button>
                </div>
                <?php form_close(); ?>
            <?php endif;?>
            <br/>
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
